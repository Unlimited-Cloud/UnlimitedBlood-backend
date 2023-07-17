<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestsRequest;
use App\Models\Inventory;
use App\Models\Requests;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Prologue\Alerts\Facades\Alert;

/**
 * Class RequestsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class RequestsCrudController extends CrudController
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
        CRUD::setModel(Requests::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/requests');
        CRUD::setEntityNameStrings('requests', 'requests');

        if (backpack_user()->hasRole('organizer')) {
            $this->crud->denyAccess(['delete', 'create']);
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
            $this->crud->addClause('where', 'fulfilled_by', '=', null);
        }

        if (backpack_user()->hasRole('organizer')) {
            $this->crud->addClause('where', 'fulfilled_by', '=', null);
        }

        CRUD::column('phoneNumber')->label('Mobile Number')->type('tel');
        CRUD::column('bloodGroup')->label('Blood Group');
        CRUD::column('bloodType')->label('Blood Type');
        CRUD::column('quantity')->label('Quantity (ml)')->type('number');
        CRUD::column('requestDate')->label('Request Date')->type('datetime');
        CRUD::column('needByDate')->label('Need By Date')->type('datetime');
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
    // Only for donors
    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'phoneNumber' => 'required',
            'bloodType' => 'required',
            'bloodGroup' => 'required',
            'address' => 'required',
            'quantity' => 'required|numeric|min:1',
            'requestDate' => 'required|date',
            'needByDate' => 'required|after_or_equal:today',

        ]);

        if (backpack_user()->hasRole('donor')) {
            CRUD::addField([
                'name' => 'phoneNumber',
                'label' => 'Mobile Number',
                'default' => backpack_user()->phoneNumber,
                'attributes' => [
                    'readonly' => 'readonly'
                ]
            ]);
        } else {
            CRUD::field('phoneNumber')->label('Mobile Number');
        }

        CRUD::addField([
            'name' => 'requestDate',
            'label' => 'Request Date',
            'type' => 'datetime',
            'value' => Carbon::now(),
            'attributes' => [
                'readonly' => 'readonly'
            ]
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
        CRUD::field('quantity')->label('Quantity (ml)')->type('number');
        CRUD::field('needByDate')->label('Need By Date')->type('datetime');
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

        $this->crud->setValidation([
            'phoneNumber' => 'required',
            'bloodType' => 'required',
            'address' => 'required',
            'quantity' => 'required',
            'requestDate' => 'required|date',
            'needByDate' => 'required|after_or_equal:today',

        ]);

        CRUD::field('phoneNumber')->attributes(["readonly" => "readonly"])->label('Mobile Number');
        CRUD::field('requestDate')->attributes(["readonly" => "readonly"])->label('Request Date');

        if (backpack_user()->hasRole('organizer')) {
            CRUD::addField([
                'name' => 'bloodGroup',
                'label' => 'Blood Group',
                'attributes' => [
                    'disabled' => 'disabled',
                ],
                'type' => 'enum',
                'options' => [
                    'A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-',
                    'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-'
                ],
                'allows_null' => false,

            ]);
            CRUD::field('bloodType')->attributes(["readonly" => "readonly"])->label('Blood Type');
            CRUD::field('quantity')->attributes(["readonly" => "readonly"])->label('Quantity (ml)');
            CRUD::field('needByDate')->attributes(["readonly" => "readonly"])->label('Need By Date');
            CRUD::field('address')->attributes(["readonly" => "readonly"]);
            CRUD::addField(
                [
                    'name' => 'fulfilled_by',
                    'label' => 'Fulfilled?',
                    'type' => 'radio',
                    'model' => "App\Models\Organization",
                    'options' => [
                        backpack_user()->organizationId => 'Yes',
                        null => 'No'

                    ],
                    'allows_null' => true,
                    'default' => null,
                ]);
        } else {
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
            CRUD::field('bloodType')->label('Blood Type');
            CRUD::field('quantity')->label('Quantity (ml)');
            CRUD::field('needByDate')->label('Need By Date')->type('datetime');
            CRUD::field('address');
        }

        Requests::updating(function (Requests $request) {


            if ($request->fulfilled_by != null) {
                $inventory = Inventory::where([
                    ['organizationId', '=', backpack_user()->organizationId],
                    ['bloodGroup', '=', $request->bloodGroup],
                    ['bloodType', '=', $request->bloodType],
                    ['quantity', '>=', $request->quantity]
                ])
                    ->first();

                if ($inventory == null) {

                    $request->fulfilled_by = null;
                    $request->save();
                    Alert::error('Insufficient inventory available')->flash();
                    Alert::warning('Please update your inventory')->flash();

                    return redirect('admin/requests');
                } else {

                    $inventory->quantity = $inventory->quantity - $request->quantity;
                    $inventory->update(['quantity' => $inventory->quantity]);
                    Alert::success('Request fulfilled successfully')->flash();
                    return redirect('admin/requests');

                }
            }
            return redirect('admin/requests');
        }
        );

    }
}
