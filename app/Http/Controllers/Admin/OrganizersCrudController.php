<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrganizersCrudControllerRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OrganizersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class OrganizersCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup(): void
    {
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/organizers');
        CRUD::setEntityNameStrings('organizer', 'organizers');

        if (backpack_user()->hasRole('organization')) {
            $this->crud->denyAccess(['update']);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation(): void
    {

        if (backpack_user()->hasRole('organizer')) {
            $this->crud->addClause('where', 'organizationId', '=', backpack_user()->organizationId);
            $this->crud->addClause('where', 'id', '!=', backpack_user()->id);
        }
        CRUD::column('name');
        CRUD::addColumn(
            [
                'name' => 'organizationId',
                'label' => 'Organization',
                'entity' => 'organization',
                'attribute' => 'name',
            ]
        );
        CRUD::column('phoneNumber')->label('Mobile Number')->type('tel');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->crud->setValidation([
            'name' => 'required|',
        ]);

        CRUD::field('name')->type('text');
        if (backpack_user()->hasRole('admin')) {
            CRUD::addField(
                [
                    'name' => 'organizationId',
                    'label' => 'Organization',
                    'type' => 'select',
                    'entity' => 'organization',
                    'attribute' => 'name',
                    'model' => 'App\Models\Organizations',

                ]
            );
        } elseif (backpack_user()->hasRole('organizer')) {


            $this->crud->addField(
                [
                    'name' => 'organizationId',
                    'attributes' => [
                        'disabled' => 'disabled',
                    ],
                    'value' => backpack_user()->organizationId,
                ]
            );
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'name' => 'required|',
            'phoneNumber' => 'required|min:10|max:10|unique:users,phoneNumber',
            'password' => 'required|min:8',
        ]);

        CRUD::field('name')->type('text');
        CRUD::field('phoneNumber')->type('number');
        CRUD::field('password')->type('password');
        if (backpack_user()->hasRole('admin')) {
            CRUD::addField(
                [
                    'name' => 'organizationId',
                    'label' => 'Organization',
                    'type' => 'select',
                    'entity' => 'organizations',
                    'attribute' => 'name',

                ]
            );
        } elseif (backpack_user()->hasRole('organizer')) {

            $this->crud->addField(
                [
                    'name' => 'organizationId',
                    'label' => 'Organization',
                    'type' => 'hidden',
                    'value' => backpack_user()->organizationId,
                ]
            );
        }


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }
}
