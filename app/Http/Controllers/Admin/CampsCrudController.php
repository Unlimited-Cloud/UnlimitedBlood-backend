<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CampsRequest;
use App\Models\Camps;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Validation\Rules\ValidUploadMultiple;
use Illuminate\Support\Facades\DB;

/**
 * Class CampsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CampsCrudController extends CrudController
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
        CRUD::setModel(Camps::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/camps');
        CRUD::setEntityNameStrings('camps', 'camps');

        if (backpack_user()->hasRole('donor')) {

            $this->crud->denyAccess(['create', 'update', 'delete']);
        }
        if (backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }

        $all_entries = $this->crud->getEntries();
        foreach ($all_entries as $entry) {
            $camp_id = $entry->getKey();
            $attendees = DB::table('donations')->where('campId', $camp_id)->count();
            $entry->attendees = $attendees;
            $entry->save(); // Save the updated entry to persist the changes
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
        CRUD::addColumn([
            'name' => 'organizationId',
            'label' => 'Organization',
            'model' => 'App\Models\Organizations',
            'entity' => 'organizations',
            'attribute' => 'name',

        ]);
        CRUD::column('address');
        CRUD::column('startDate')->label('Start Date');
        CRUD::column('endDate')->label('End Date');
        CRUD::column('attendees');
        CRUD::column('pictures')->type('image');

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
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
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
            'startDate' => 'required',
            'endDate' => 'required',
            'pictures' => ValidUploadMultiple::field('required|min:1|max:5')
                ->file('file|mimes:jpeg,png,jpg|max:2048'),
            'address' => 'required',
            'name' => 'required',
        ]);

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

        CRUD::field('pictures')->type('upload_multiple')->withFiles([
            'temporaryUrl' => true,
            'temporaryUrlExpirationTime' => 43200
        ]);
        // create route to uploaded images


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * @return void
     */

}
