<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DonationsRequest;
use App\Models\Donations;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DonationsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class DonationsCrudController extends CrudController
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
        CRUD::setModel(Donations::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/donations');
        CRUD::setEntityNameStrings('donations', 'donations');

        if (backpack_user()->hasRole('donor')) {
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
        if (backpack_user()->hasRole('donor')) {
            $user_number = backpack_user()->phoneNumber;
            $this->crud->addClause('where', 'phoneNumber', '=', $user_number);
        }

        CRUD::column('phoneNumber')->label('Mobile Number')->type('tel');
        CRUD::column('bloodType')->label('Blood Type');
        CRUD::column('donationType')->label('Donation Type');
        CRUD::column('quantity')->label('Quantity (ml)');
        CRUD::column('donationDate')->label('Date');
        if (backpack_user()->hasRole('admin')) {
            CRUD::column('organizationId')->label('Organization ID');
            CRUD::column('campId')->label('Camp ID');
        }
        CRUD::column('upperBP')->label('Upper BP');
        CRUD::column('lowerBP')->label('Lower BP');
        CRUD::column('weight');
        CRUD::column('notes');

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
            'phoneNumber' => 'required|numeric',
            'bloodType' => 'required',
            'donationType' => 'required',
            'quantity' => 'required|numeric',
            'donationDate' => 'required|date',
            'upperBP' => 'required|numeric',
            'lowerBP' => 'required|numeric',
        ]);

        CRUD::addField([
            'name' => 'organizationId',
            'label' => 'Organization ID',
            'attributes' => [
                'readonly' => 'readonly'
            ],
            'default' => backpack_user()->organizations->id,
        ]);
        CRUD::addField([
            'name' => 'campId',
            'label' => 'Camp ID',
            'type' => 'select',

        ]);
        CRUD::field('phoneNumber')->label('Mobile Number')->type('number');
        CRUD::addField([
            'name' => 'bloodType',
            'label' => 'Blood Type',
            'type' => 'enum',
            'options' => [
                'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-',
                'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'
            ],
        ]);
        CRUD::field('donationType');
        CRUD::addField([
            'name' => 'quantity',
            'label' => 'Quantity (ml)',
            'type' => 'number',

        ]);
        CRUD::field('donationDate')->label('Donation Date')->type('date')->default(today());
        CRUD::field('upperBP')->label('Upper Blood Pressure')->type('number');
        CRUD::field('lowerBP')->label('Upper Blood Pressure')->type('number');
        CRUD::field('weight')->type('number');
        CRUD::field('notes');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }
}
