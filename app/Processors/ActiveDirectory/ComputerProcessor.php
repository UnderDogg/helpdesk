<?php

namespace App\Processors\ActiveDirectory;

use Adldap\Contracts\Adldap;
use Adldap\Models\Computer;
use Adldap\Schemas\ActiveDirectory;
use App\Http\Presenters\ActiveDirectory\ComputerPresenter;
use App\Http\Requests\ActiveDirectory\ComputerImportRequest;
use App\Jobs\ActiveDirectory\ImportComputer;
use App\Policies\ActiveDirectory\ComputerPolicy;
use App\Processors\Processor;
use Illuminate\Http\Request;

class ComputerProcessor extends Processor
{
    /**
     * @var ComputerPresenter
     */
    protected $presenter;

    /**
     * @var Adldap
     */
    protected $adldap;

    /**
     * Constructor.
     *
     * @param ComputerPresenter $presenter
     * @param Adldap            $adldap
     */
    public function __construct(ComputerPresenter $presenter, Adldap $adldap)
    {
        $this->presenter = $presenter;
        $this->adldap = $adldap;
    }

    /**
     * Displays all computers in active directory.
     *
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (ComputerPolicy::index(auth()->user())) {
            $search = $this->adldap->computers()->search();

            if ($request->has('q')) {
                $query = $request->input('q');

                $search = $search
                    ->orWhereContains(ActiveDirectory::COMMON_NAME, $query)
                    ->orWhereContains(ActiveDirectory::DESCRIPTION, $query)
                    ->orWhereContains(ActiveDirectory::OPERATING_SYSTEM, $query);
            }

            $paginator = $search->sortBy(ActiveDirectory::COMMON_NAME, 'asc')->paginate();

            $computers = $this->presenter->table($paginator->getResults());

            $navbar = $this->presenter->navbar();

            return view('pages.active-directory.computers.index', compact('computers', 'navbar'));
        }

        $this->unauthorized();
    }

    /**
     * Imports an active directory computer.
     *
     * @param ComputerImportRequest $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return bool|mixed
     */
    public function store(ComputerImportRequest $request)
    {
        if (ComputerPolicy::import(auth()->user())) {
            $computer = $this->adldap->search()->findByDn($request->input('dn'));

            if ($computer instanceof Computer) {
                return $this->dispatch(new ImportComputer($computer));
            }

            return false;
        }

        $this->unauthorized();
    }

    /**
     * Imports all active directory computers.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return array
     */
    public function storeAll()
    {
        if (ComputerPolicy::importAll(auth()->user())) {
            $computers = $this->adldap->computers()->all();

            $added = [];

            foreach ($computers as  $computer) {
                if ($computer instanceof Computer) {
                    $added[] = $this->dispatch(new ImportComputer($computer));
                }
            }

            return $added;
        }

        $this->unauthorized();
    }
}
