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

        if (!backpack_user()->hasRole('organizer')) {
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
        if (backpack_user()->hasRole('organizer')) {
            $this->crud->addClause('where', 'organizationId', '=', backpack_user()->organizationId);
        }
        if (backpack_user()->hasRole('donor')) {
            $this->crud->addClause('where', 'phoneNumber', '=', backpack_user()->phoneNumber);
        }

        CRUD::column('phoneNumber')->label('Mobile Number')->type('tel');
        CRUD::column('bloodGroup')->label('Blood Group');
        CRUD::column('bloodType')->label('Blood Type');
        CRUD::column('quantity')->label('Quantity (ml)');
        CRUD::column('donationDate')->label('Date')->type('date');
        if (backpack_user()->hasRole('admin')) {
            CRUD::addColumn([
                'name' => 'organizationId',
                'label' => 'Organization',
                'model' => 'App\Models\Organizations',
                'entity' => 'organization',
                'attribute' => 'name',
            ]);
            CRUD::addColumn([
                'name' => 'campId',
                'label' => 'Camp',
                'model' => 'App\Models\Camps',
                'entity' => 'camp',
                'attribute' => 'name',
            ]);
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
            'phoneNumber' => 'required|numeric|digits:10|exists:donors,phoneNumber',
            'bloodGroup' => 'required',
            'bloodType' => 'required',
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
            'default' => backpack_user()->organizationId,
        ]);
        CRUD::addField([
            'name' => 'campId',
            'label' => 'Camp ID',
            'type' => 'select',
            'entity' => 'camp',
            'attribute' => 'name',
            'model' => "App\Models\Camps",

        ]);
        CRUD::field('phoneNumber')->label('Mobile Number')->type('number');
        CRUD::addField([
            'name' => 'bloodGroup',
            'label' => 'Blood Group',
            'type' => 'enum',
            'options' => [
                'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-',
                'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'
            ],
        ]);
        CRUD::addField([
            'name' => 'bloodType',
            'label' => 'Blood Type',
            'type' => 'enum',
            'options' => [
                'Whole Blood' => 'Whole Blood', 'Platelets' => 'Platelets', 'Plasma' => 'Plasma'
            ],
        ]);
        CRUD::addField([
            'name' => 'quantity',
            'label' => 'Quantity (ml)',
            'type' => 'number',

        ]);
        CRUD::field('donationDate')->label('Donation Date')->type('date')->default(today());
        CRUD::field('upperBP')->label('Upper Blood Pressure')->type('number');
        CRUD::field('lowerBP')->label('Upper Blood Pressure')->type('number');
        CRUD::field('weight')->type('number')->label('Weight (kg)');
        CRUD::field('notes');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }
}
