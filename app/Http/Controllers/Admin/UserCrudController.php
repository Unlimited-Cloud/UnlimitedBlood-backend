<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as BaseUserCrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class UserCrudController extends BaseUserCrudController
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
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');

        if (!backpack_user()->hasRole('admin')) {
            redirect()->route('backpack.dashboard')->send();
            $this->crud->denyAccess(['show', 'create', 'update', 'delete', 'reorder', 'revisions', 'details']);

        }

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    public function setupListOperation(): void
    {
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type' => 'text',
            ],
            [
                'name' => 'phoneNumber',
                'label' => 'Mobile Number',
                'type' => 'tel',
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.roles'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'roles', // the method that defines the relationship in your Model
                'entity' => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => config('permission.models.role'), // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'permissions', // the method that defines the relationship in your Model
                'entity' => 'permissions', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => config('permission.models.permission'), // foreign key model
            ],
        ]);

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
    public function setupCreateOperation(): void
    {
        $this->addUserFields();
        //$this->crud->setValidation(StoreRequest::class);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    protected function addUserFields(): void
    {
        $this->crud->addFields([
                [
                    'name' => 'name',
                    'label' => trans('backpack::permissionmanager.name'),
                    'type' => 'text',
                ],
                [
                    'name' => 'phoneNumber',
                    'label' => 'Mobile Number',
                    'type' => 'number',
                ],
                [

                    'name' => 'password',
                    'label' => trans('backpack::permissionmanager.password'),
                    'type' => 'password',
                ],
                [
                    'name' => 'password_confirmation',
                    'label' => trans('backpack::permissionmanager.password_confirmation'),
                    'type' => 'password',
                ],
                [
                    // two interconnected entities
                    'label' => trans('backpack::permissionmanager.user_role_permission'),
                    'field_unique_name' => 'user_role_permission',
                    'type' => 'checklist_dependency',
                    'name' => 'roles,permissions',
                    'subfields' => [
                        'primary' => [
                            'label' => trans('backpack::permissionmanager.roles'),
                            'name' => 'roles', // the method that defines the relationship in your Model
                            'entity' => 'roles', // the method that defines the relationship in your Model
                            'entity_secondary' => 'permissions',
                            // the method that defines the relationship in your Model
                            'attribute' => 'name', // foreign key attribute that is shown to user
                            'model' => config('permission.models.role'), // foreign key model
                            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                            'number_columns' => 3, //can be 1,2,3,4,6

                        ],
                        'secondary' => [
                            'label' => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                            'name' => 'permissions', // the method that defines the relationship in your Model
                            'entity' => 'permissions', // the method that defines the relationship in your Model
                            'entity_primary' => 'roles', // the method that defines the relationship in your Model
                            'attribute' => 'name', // foreign key attribute that is shown to user
                            'model' => config('permission.models.permission'), // foreign key model
                            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?]
                            'number_columns' => 3, //can be 1,2,3,4,6
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    public function setupUpdateOperation(): void
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return RedirectResponse
     */

    public function store(): RedirectResponse
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitStore();
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        /*$request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');*/

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    /**
     * Update the specified resource in the database.
     *
     * @return RedirectResponse
     */
    public function update(): RedirectResponse
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitUpdate();
    }
}
