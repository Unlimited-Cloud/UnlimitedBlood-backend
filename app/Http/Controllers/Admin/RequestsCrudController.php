<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RequestsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Requests::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/requests');
        CRUD::setEntityNameStrings('requests', 'requests');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['delete', 'create',]);
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
            $user_number = backpack_user()->phoneNumber;
            $this->crud->addClause('where', 'phoneNumber', '=', $user_number);
        }
        
        CRUD::column('phoneNumber')->label('Mobile Number');
        CRUD::column('bloodType')->label('Blood Type');
        CRUD::column('donationType')->label('Donation Type');
        CRUD::column('quantity')->label('Quantity (ml)');
        CRUD::column('requestDate')->label('Request Date');
        CRUD::column('address');
        CRUD::column('fulfilled_by');

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
        CRUD::field('phoneNumber')->label('Mobile Number');
        CRUD::addField([
            'name' => 'bloodType',
            'label' => 'Blood Type',
            'type' => 'enum',
            'options' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'allows_null' => false,

        ]);
        CRUD::field('donationType')->label('Donation Type');
        CRUD::field('quantity')->label('Quantity (ml)');
        CRUD::field('requestDate')->label('Request Date');
        CRUD::field('address');
        if (backpack_user()->hasRole('admin')) {
            CRUD::field('fulfilled_by');
        }

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
        CRUD::field('phoneNumber')->attributes(["readonly" => "readonly"])->label('Mobile Number');
        CRUD::addField([
            'name' => 'bloodType',
            'label' => 'Blood Type',
            'attributes' => [
                'disabled' => 'disabled',
            ],
            'type' => 'enum',
            'options' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'allows_null' => false,

        ]);
        CRUD::field('donationType')->attributes(["readonly" => "readonly"])->label('Donation Type');
        CRUD::field('quantity')->attributes(["readonly" => "readonly"])->label('Quantity (ml)');
        CRUD::field('requestDate')->attributes(["readonly" => "readonly"])->label('Request Date');
        CRUD::field('address')->attributes(["readonly" => "readonly"]);
        CRUD::addField(
            [
                'name' => 'fulfilled_by',
                'label' => 'Fulfilled?',
                'type' => 'radio',
                'model' => "App\Models\User",
                'options' => [
                    backpack_user()->organizations->id => 'Yes',
                    null => 'No'

                ],
                'allows_null' => true,
                'default' => null,
            ]
        );

    }
}
