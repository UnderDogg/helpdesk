<?php

namespace App\Http\Presenters\Device;

use App\Http\Presenters\Presenter;
use App\Models\Computer;
use App\Models\ComputerPatch;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Column;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;

class ComputerPatchPresenter extends Presenter
{
    /**
     * Returns a new table of all the specified computers patches.
     *
     * @param Computer $computer
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(Computer $computer)
    {
        $patches = $computer->patches()->latest();

        return $this->table->of('computers.patches', function (TableGrid $table) use ($computer, $patches) {
            $table->with($patches)->paginate($this->perPage);

            $table->searchable([
                'title',
                'description',
            ]);

            $table->column('title', function (Column $column) use ($computer) {
                $column->value = function (ComputerPatch $patch) use ($computer) {
                    return link_to_route(
                        'devices.computers.patches.show',
                        $patch->title,
                        [$computer->getKey(), $patch->getKey()]
                    );
                };
            });

            $table->column('created_at_human')
                ->label('Created');
        });
    }

    /**
     * Returns a new form of the specified computer patch.
     *
     * @param Computer      $computer
     * @param ComputerPatch $patch
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Computer $computer, ComputerPatch $patch)
    {
        return $this->form->of('computers.patches', function (FormGrid $form) use ($computer, $patch) {
            if ($patch->exists) {
                $method = 'PATCH';
                $url = route('devices.computers.patches.update', [$computer->getKey(), $patch->getKey()]);

                $form->submit = 'Save';
            } else {
                $method = 'POST';
                $url = route('devices.computers.patches.store', [$computer->getKey()]);

                $form->submit = 'Create';
            }

            $form->with($patch);

            $form->attributes(compact('method', 'url'));

            $form->fieldset(function (Fieldset $fieldset) {
                $fieldset->control('input:text', 'title')
                    ->attributes([
                        'placeholder' => 'Enter a patch title.',
                    ]);

                $fieldset->control('input:textarea', 'description')
                    ->attributes([
                        'placeholder' => 'Enter a patch description.',
                    ]);
            });
        });
    }

    /**
     * Returns a new navbar for the computer patch index.
     *
     * @param Computer $computer
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar(Computer $computer)
    {
        return $this->fluent([
            'id'         => 'computer-patches',
            'title'      => "{$computer->name} | Patches",
            'url'        => route('devices.computers.patches.index', [$computer->getKey()]),
            'menu'       => view('pages.devices.computers.patches._nav', compact('computer')),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }

    /**
     * Returns a new navbar when displaying the specified computer patch.
     *
     * @param Computer      $computer
     * @param ComputerPatch $patch
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbarShow(Computer $computer, ComputerPatch $patch)
    {
        return $this->fluent([
            'id'         => 'computer-patches-show',
            'title'      => "{$patch->title}",
            'url'        => route('devices.computers.patches.show', [$computer->getKey(), $patch->getKey()]),
            'menu'       => view('pages.devices.computers.patches._nav-show', compact('computer', 'patch')),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}