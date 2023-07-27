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
        if (backpack_user()->hasRole('organizer')) {
            $this->crud->addClause('where', 'organizationId', '=', backpack_user()->organizationId);
        }

        CRUD::column('name');
        if (backpack_user()->hasRole('admin')) {
            CRUD::addColumn([
                'name' => 'organizationId',
                'label' => 'Organization',
                'model' => 'App\Models\Organizations',
                'entity' => 'organization',
                'attribute' => 'name',

            ]);
        }

        CRUD::column('address');
        CRUD::column('startDate')->label('Start Date')->type('datetime');
        CRUD::column('endDate')->label('End Date')->type('datetime');
        CRUD::column('attendees');
        CRUD::column('pictures')->type('base64_image');

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
            'address' => 'required',
            'name' => 'required',
        ]);

        CRUD::addField([
            'name' => 'organizationId',
            'label' => 'Organization ID',
            'attributes' => [
                'readonly' => 'readonly',
            ],
            'default' => backpack_user()->organizationId,

        ]);
        CRUD::field('name')->label("Camp Name");
        CRUD::field('address');
        # get startDate that's already in the database
        CRUD::addfield([
            'name' => 'startDate',
            'label' => 'Start Date and Time',
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'YYYY-MM-DD HH:mm:ss',
                'language' => 'en',
            ],
        ]);
        CRUD::addField([
            'name' => 'endDate',
            'label' => 'End Date and Time',
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'YYYY-MM-DD HH:mm:ss',
                'language' => 'en',
            ],
        ]);
        CRUD::addfield([
            'label' => "Poster",
            'name' => "pictures",
            'filename' => null, // set to null if not needed
            'type' => 'base64_image',
            'aspect_ratio' => 16 / 9, // set to 0 to allow any aspect ratio
            'crop' => true, // set to true to allow cropping, false to disable
            'src' => null, // null to read straight from DB, otherwise set to model accessor function
        ]);
        CRUD::field('latitude')->type('number');
        CRUD::field('longitude')->type('number');


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
