<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrganizationsRequest;
use App\Models\Organizations;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OrganizationsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class OrganizationsCrudController extends CrudController
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
        CRUD::setModel(Organizations::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/organizations');
        CRUD::setEntityNameStrings('organizations', 'organizations');

        if (backpack_user()->hasRole('donor') || backpack_user()->hasRole('organizer')) {
            redirect()->route('backpack.dashboard')->send();
            $this->crud->denyAccess(['show', 'create', 'update', 'delete']);
        } elseif (backpack_user()->hasRole('unverified')) {
            $this->crud->denyAccess(['show', 'delete']);

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
        if (backpack_user()->hasRole('unverified')) {
            $this->crud->addClause('where', 'id', '=', backpack_user()->organizationId);
        }
        if (backpack_user()->organizationId != null) {
            $this->crud->denyAccess(['create', 'delete']);
        }
        CRUD::column('id')->label('ID')->type('number');
        CRUD::column('phoneNumber')->label('Mobile Number')->type('tel');
        CRUD::column('email')->type('email');
        CRUD::column('name');
        CRUD::column('address');
        CRUD::column('website')->type('url');
        CRUD::column('logo')->type('base64_image');

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
            'phoneNumber' => 'required|digits:10',
            'email' => 'required',
            'name' => 'required',
            'address' => 'required',

        ]);
        CRUD::field('phoneNumber')->label('Organization Phone Number')->type('number');
        CRUD::field('email')->type('email');
        CRUD::field('name')->label('Organization Name')->type('text');
        CRUD::field('address')->type('text');
        CRUD::addField([
            'name' => 'logo',
            'label' => 'Organization Logo',
            'type' => 'base64_image',
            'filename' => null,
            'aspect_ratio' => 1, // set to 0 to allow any aspect ratio
            'crop' => true, // set to true to allow cropping, false to disable
            'src' => null,
        ]);
        CRUD::field('website')->type('url');
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
            'phoneNumber' => 'required|unique:organizations,phoneNumber|digits:10',
            'email' => 'required',
            'name' => 'required',
            'address' => 'required',

        ]);
        CRUD::field('phoneNumber')->label('Organization Phone Number')->type('number');
        CRUD::field('email')->type('email');
        CRUD::field('name')->label('Organization Name')->type('text');
        CRUD::field('address')->type('text');
        CRUD::field('latitude')->type('number');
        CRUD::field('longitude')->type('number');
        // TODO: fix google map field by fixing API
        CRUD::addfield([
            'name' => 'address',
            'type' => 'google_map',
            // optionals
            'map_options' => [
                'default_lat' => 27.7,
                'default_lng' => 85.3,
                'locate' => true, // when false, only a map is displayed. No value for submission.
                'height' => 400 // in pixels
            ]
        ]);
        CRUD::addField([
            'name' => 'logo',
            'label' => 'Organization Logo',
            'type' => 'base64_image',
            'filename' => null,
            'aspect_ratio' => 1, // set to 0 to allow any aspect ratio
            'crop' => true, // set to true to allow cropping, false to disable
            'src' => null,
        ]);
        CRUD::field('website')->type('url');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
        Organizations::created(function (Organizations $organizations) {
            backpack_user()->organizationId = $organizations->id;
            backpack_user()->save();

            return redirect()->route('backpack.dashboard');
        });

    }

}
