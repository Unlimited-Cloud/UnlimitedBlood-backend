<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CampsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CampsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CampsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup(): void
    {
        CRUD::setModel(\App\Models\Camps::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/camps');
        CRUD::setEntityNameStrings('camps', 'camps');

        if (backpack_user()->hasRole('donor')) {

            $this->crud->denyAccess(['create', 'update', 'delete']);
        }
        if (backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
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
        if (backpack_user()->hasRole('organization')) {
            $user_organization_id = backpack_user()->organizations->id;
            $this->crud->addClause('where', 'organizationId', '=', $user_organization_id);
        }

        CRUD::column('name');
        CRUD::column('address');
        CRUD::column('startDate')->label('Start Date');
        CRUD::column('endDate')->label('End Date');
        CRUD::column('attendees');
        CRUD::column('pictures')->type('image');
        CRUD::column('organizationId');
        if (backpack_user()->hasRole('admin')) {

            CRUD::column('created_at');
            CRUD::column('updated_at');
        }


        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::addField([
            'name' => 'organizationId',
            'label' => 'Organization ID',
            'attributes' => [
                'readonly' => 'readonly',
            ],
            'default' => backpack_user()->organizations->id,

        ]);
        CRUD::field('name');
        CRUD::field('address');
        CRUD::field('startDate')->type('date');
        CRUD::field('endDate')->type('date');
        //CRUD::field('attendees');
        // to upload multiple images and store them
        CRUD::field('pictures')->type('upload_multiple')->withFiles();


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
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
        CRUD::addField([
            'name' => 'organizationId',
            'label' => 'Organization ID',
            'attributes' => [
                'readonly' => 'readonly',
            ],
            'default' => backpack_user()->organizations->id,

        ]);
        CRUD::field('name');
        CRUD::field('address');
        # get startDate that's already in the database
        CRUD::field('startDate')->type('date');
        CRUD::field('endDate')->type('date');
        //CRUD::field('attendees');
        CRUD::field('pictures')->type('upload_multiple')->withFiles();
    }
}
