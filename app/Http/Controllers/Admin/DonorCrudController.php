<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DonorRequest;
use App\Models\Donor;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DonorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class DonorCrudController extends CrudController
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
        CRUD::setModel(Donor::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/donor');
        CRUD::setEntityNameStrings('donor', 'donors');

        if (backpack_user()->hasRole('organization')) {
            redirect()->route('backpack.dashboard')->send();
            $this->crud->denyAccess(['show', 'create', 'update', 'delete']);
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
        if (backpack_user()->hasRole('donor')) {
            $user_phone = backpack_user()->phoneNumber;
            $this->crud->addClause('where', 'phoneNumber', '=', $user_phone);
        }

        CRUD::addColumn([
            'name' => 'phoneNumber',
            'label' => 'Mobile Number',
            'type' => 'tel',
        ]);
        CRUD::column('email')->type('email');
        CRUD::column('fname');
        //CRUD::column('mname');
        CRUD::column('lname');
        CRUD::addColumn([
            'name' => 'bloodGroup',
            'label' => 'Blood Group',
            'type' => 'enum',
            'options' => [
                'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-',
                'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'
            ]
        ]);
        CRUD::column('address');
        CRUD::column('gender');
        CRUD::column('birthDate');
        CRUD::column('user_id');

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
        CRUD::field('phoneNumber')->type('number');
        CRUD::field('email')->type('email');
        CRUD::field('fname')->type('text');
        CRUD::field('mname')->type('text');
        CRUD::field('lname')->type('text');
        CRUD::field('password');
        CRUD::addField([
            'name' => 'bloodGroup',
            'label' => 'Blood Group',
            'type' => 'enum',
            'options' => [
                'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-',
                'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'
            ]
        ]);
        CRUD::field('address');
        CRUD::addField([
            'name' => 'gender',
            'label' => 'Gender',
            'type' => 'enum',
            'options' => ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']
        ]);
        CRUD::field('birthDate')->type('date');
        CRUD::field('user_id');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }
}
