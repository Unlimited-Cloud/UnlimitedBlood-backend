<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InventoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class InventoryCrudController extends CrudController
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
        CRUD::setModel(Inventory::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/inventory');
        CRUD::setEntityNameStrings('inventory', 'inventory');

        if (backpack_user()->hasRole('donor')) {
            redirect()->route('backpack.dashboard')->send();
            $this->crud->denyAccess(['show', 'create', 'update', 'delete']);
        }

        if (backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'update']);
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
        CRUD::column('id')->label('ID');

        if (backpack_user()->hasRole('admin')) {
            CRUD::addColumn([
                'name' => 'organizationId',
                'label' => 'Organization',
                'model' => 'App\Models\Organizations',
                'entity' => 'organization',
                'attribute' => 'name',

            ]);
        }
        CRUD::column('bloodGroup')->label('Blood Group');
        CRUD::column('bloodType')->label('Blood Type');
        CRUD::column('quantity')->label('Quantity (ml)');
        CRUD::column('price')->label('Price (Rs)');
        CRUD::column('updated_at')->label('Last Updated');

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
            'bloodGroup' => 'required',
            'bloodType' => 'required',
            'quantity' => 'required',
            'price' => 'required',

        ]);

        CRUD::addField([
            'name' => 'organizationId',
            'label' => 'Organization ID',
            'type' => 'hidden',
            'default' => backpack_user()->organizationId,
        ]);
        CRUD::addField([
            'name' => 'bloodGroup',
            'label' => 'Blood Group',
            'type' => 'enum',
            'options' => [
                'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-',
                'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'
            ],
            'allows_null' => false,
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
        CRUD::addField([
            'name' => 'price',
            'label' => 'Price (Rs)',
            'type' => 'number',
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }
}
