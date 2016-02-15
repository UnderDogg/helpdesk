<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\GuideRequest;
use App\Processors\Resource\GuideProcessor;

class GuideController extends Controller
{
    /**
     * @var GuideProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param GuideProcessor $processor
     */
    public function __construct(GuideProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all guides.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Displays all favorited guides.
     *
     * @return \Illuminate\View\View
     */
    public function favorites()
    {
        return $this->processor->index($favorites = true);
    }

    /**
     * Displays the form for creating a new guide.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GuideRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GuideRequest $request)
    {
        if ($this->processor->store($request)) {
            flash()->success('Success!', 'Successfully created guide!');

            return redirect()->route('resources.guides.index');
        } else {
            flash()->error('Error!', 'There was an issue creating a guide. Please try again.');

            return redirect()->route('resources.guides.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->processor->edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GuideRequest $request
     * @param int|string   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GuideRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated guide!');

            return redirect()->route('resources.guides.show', [$id]);
        } else {
            flash()->error('Error!', 'There was an issue updating this guide. Please try again.');

            return redirect()->route('resources.guides.edit', [$id]);
        }
    }

    /**
     * Favorites the specified guide.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function favorite($id)
    {
        if ($this->processor->favorite($id)) {
            return redirect()->route('resources.guides.show', [$id]);
        } else {
            flash()->error('Error!', 'There was an issue with adding this guide to your favorites. Please try again.');

            return redirect()->route('resources.guides.show', [$id]);
        }
    }

    /**
     * Deletes the specified guide.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->processor->destroy($id)) {
            flash()->success('Success!', 'Successfully deleted guide!');

            return redirect()->route('resources.guides.index');
        } else {
            flash()->error('Error!', 'There was an issue deleting this guide. Please try again.');

            return redirect()->route('resources.guides.show', [$id]);
        }
    }
}
