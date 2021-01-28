<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class PostCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PostCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\ReviseOperation\ReviseOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Post::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/post');
        CRUD::setEntityNameStrings('post', 'posts');
        if(backpack_user()->hasRole("User")){
            $this->crud->denyAccess("create");
            $this->crud->denyAccess("update");
            $this->crud->denyAccess("delete");
            $this->crud->denyAccess("clone");
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
                $this->crud->addButton('line', 'update', 'view', 'crud::buttons.edit');
                $this->crud->addButton('line', 'delete', 'view', 'crud::buttons.delete');
        CRUD::addColumn([
            'name'=>"title",
            "label"=>"Title",
            "type"=>"text",
            "limit"=>30,

        ]);
        $this->crud->addColumn([
            'name' => 'image', // The db column name
            'label' => "Post Image", // Table column heading
            'type' => 'image',
            "disk"         => $this->crud->getCurrentEntry(),
            "upload"       =>true,
            'height' => '150px',
            'width'  => '130px'
        ]);
        $this->crud->addFilter(
            [
                'name'  => 'category',
                'type'  => 'select2_ajax',
                'label' => "Category",
                'placeholder' => 'Pick a category'
            ],
            url('admin/posts/ajax-category-options'), // the ajax route
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'category', function ($query) use ($value) {
                    $query->where('category_id', '=', $value);
                });
            }
            );
        $this->crud->addFilter([
            'name'        => 'tag',
            'type'        => 'select2_ajax',
            'label'       => 'Tag',
            'placeholder' => 'Pick a tag'
        ],
            url('admin/posts/ajax-tag-options'), // the ajax route
               function ($value) { // if the filter is active
                   $this->crud->addClause('whereHas', 'tag', function ($query) use ($value) {
                       $query->where('tag_id', '=', $value);
                   });
               }
          );
        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'created_at',
            'label' => 'Date range'
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                 $dates = json_decode($value);
                 $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                 $this->crud->addClause('where', 'created_at', '<=', $dates->to . ' 23:59:59');
            });
        $this->crud->addColumns([
            [ // n-n relationship (with pivot table)
                'label'     => "Category", // Table column heading
                'type'      => 'relationship',
                'name'      => 'category', // the method that defines the relationship in your Model
                'entity'    => 'category', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => Category::class, // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label'     => "Tags", // Table column heading
                'type'      => 'relationship',
                'name'      => 'tag', // the method that defines the relationship in your Model
                'entity'    => 'tag', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => Tag::class, // foreign key model
            ],
        ]);
        $this->crud->addColumn(
                [
                    'name'  => 'custom_fields',
                    'label' => 'Custom Fields',
                    'type'  => 'table',
                    'columns' =>
                        [
                            'name'=>"name",
                            'content'=>"content",
                        ],
                ]
        );
        $this->crud->addColumns([
            [
                'name'  => 'status',
                'label' => 'Status',
                'type'  => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => 'Published', 0 => 'Private'],
                'wrapper' => [
                    'element' => 'span',
                    'class'   => function ($crud, $column, $entry, $related_key) {
                        if ($column['text'] == 'Published') {
                            return 'badge badge-success';
                        }
                        return 'badge badge-default';
                    },
                ],
            ],
            [
                'name'  => 'allow_comments',
                'label' => 'Allow Comments',
                'type'  => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => 'Yes', 0 => 'No'],
                'wrapper' => [
                    'element' => 'span',
                    'class'   => function ($crud, $column, $entry, $related_key) {
                        if ($column['text'] == 'Yes') {
                            return 'badge badge-success';
                        }
                        return 'badge badge-default';
                    },
                ],
            ]
        ]);
        CRUD::addColumn('created_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }
    public function setupShowOperation(){
        $this->crud->addButton('line', 'update', 'view', 'crud::buttons.edit',"beginning");
        $this->crud->addButton('line', 'delete', 'view', 'crud::buttons.delete');
        CRUD::addColumn([
            'name'=>"title",
            "label"=>"Title",
            "type"=>"textarea",
            "tab"=>"simple"

        ]);
        CRUD::addColumn([
            'name'=>"description",
            "label"=>"Description",
            "type"=>"textarea",

        ]);
        $this->crud->addColumn(
            [
                'name'  => 'custom_fields',
                'label' => 'Custom Fields',
                'type'  => 'table',
                'columns' =>
                    [
                        'name'=>"name",
                        'content'=>"content",
                    ],
            ]
        );
        CRUD::addColumn([
            "name"=>"user_id",
            'type'=> 'select',
            "label"=>"Author",
            'entity' => "User",
            'attribute' => 'name',
            'wrapper'   => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('user/'.$entry->user_id.'/show');
                },
            ],
            'model' => "App\Models\User",
        ]);
        $this->crud->addColumns([
            [ // n-n relationship (with pivot table)
                'label'     => "Category", // Table column heading
                'type'      => 'relationship',
                'name'      => 'category', // the method that defines the relationship in your Model
                'entity'    => 'category', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => Category::class, // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label'     => "Tags", // Table column heading
                'type'      => 'relationship',
                'name'      => 'tag', // the method that defines the relationship in your Model
                'entity'    => 'tag', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => Tag::class, // foreign key model
            ],
        ]);
        $this->crud->addColumn([
            'name' => 'image', // The db column name
            'label' => "Post Image", // Table column heading
            'type' => 'image',
            "disk"         =>$this->crud->getCurrentEntry()->disk,
            "upload"       =>true,
            'height' => '150px',
            'width'  => '130px'
        ]);
        $this->crud->addColumns([
            [
            'name'         => 'format_id', // name of relationship method in the model
            'type'         => 'select',
            'label'        => 'Format',
            'entity' => "Format",
            'attribute' =>'name',
            'model' => "App\Models\Format",
            ],
            [
                'name'  => 'status',
                'label' => 'Status',
                'type'  => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => 'Published', 0 => 'Private'],
                'wrapper' => [
                    'element' => 'span',
                    'class'   => function ($crud, $column, $entry, $related_key) {
                        if ($column['text'] == 'Published') {
                            return 'badge badge-success';
                        }
                        return 'badge badge-default';
                    },
                ],
            ],
            [
                'name'  => 'allow_comments',
                'label' => 'Allow Comments',
                'type'  => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => 'Yes', 0 => 'No'],
                'wrapper' => [
                    'element' => 'span',
                    'class'   => function ($crud, $column, $entry, $related_key) {
                        if ($column['text'] == 'Yes') {
                            return 'badge badge-success';
                        }
                        return 'badge badge-default';
                    },
                ],
            ]
        ]);
        CRUD::addColumn('created_at');
        CRUD::addColumn('updated_at');

    }
    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PostRequest::class);
        $this->crud->addFields([
                [
                    'label'     => "Category",
                    'type'      => 'relationship',
                    'name'      => 'category', // the method that defines the relationship in your Model

                    // optional
                    'entity'    => 'category', // the method that defines the relationship in your Model
                    'model'     => Category::class, // foreign key model
                    'attribute' => 'name', // foreign key attribute that is shown to user
                    'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
                    "inline_create"=>true,
                    'ajax' => true,

                    // also optional
                    'options'   => (function ($query) {
                        return $query->orderBy('id', 'ASC')->get();
                    }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'label'     => "Format",
                    'type'      => 'select',
                    'name'      => 'format_id', // the db column for the foreign key

                    // optional
                    'entity'    => 'Format', // the method that defines the relationship in your Model
                    'model'     => "\App\Models\Format", // foreign key model
                    'attribute' => 'name', // foreign key attribute that is shown to user
                    'default'   => 1, // set the default value of the select2
                    // also optional
                    'options'   => (function ($query) {
                        return $query->orderBy('id', 'ASC')->get();
                    }),
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'label'     => "Tags",
                    'type'      => 'relationship',
                    'name'      => 'tag', // the method that defines the relationship in your Model

                    // optional
                    'entity'    => 'tag', // the method that defines the relationship in your Model
                    'model'     => Tag::class, // foreign key model
                    'attribute' => 'name', // foreign key attribute that is shown to user
                    'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
                    "inline_create"=>true,
                    'ajax' => true,

                    // also optional
                    'options'   => (function ($query) {
                        return $query->orderBy('id', 'ASC')->get();
                    }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
        ]);
        $this->crud->addField([
            "label"=>"Disk",
            "name"=>"disk",
            "type"=>"hidden",
            "value"=>config("save_disk.post_thumb"),
        ]);
        CRUD::addField([
            "name"=>"title",
            'type'  => 'text',
            "label"=>"title"
        ]);
        $abc = CRUD::addField(
            [   // repeatable
                'name'  => 'custom_fields',
                'label' => 'Testimonials',
                'type'  => 'repeatable',
                'fields' => [
                    [
                        'name'    => 'name',
                        'type'    => 'text',
                        'label'   => 'Name',
                        'wrapper' => ['class' => 'form-group col-md-6'],
                    ],
                    [
                        'name'    => 'content',
                        'type'    => 'text',
                        'label'   => 'Content',
                        'wrapper' => ['class' => 'form-group col-md-6'],
                    ]
                ],
            ]
        );
        CRUD::addField([
            "name"=>"excerpt",
            'type'  => 'text',
            "label"=>"Excerpt"
        ]);
        CRUD::addField([
            "name"=>"url",
            'type'  => 'text',
            "label"=>"Send TrackBacks"
        ]);
        CRUD::addField([
            "name"=>"description",
            'type'  => 'ckeditor',
            "label"=>"Description",
        ]);
        $this->crud->addField([
            'label'        => "Post Image",
            'name'         => "image",
            'filename'     => "image_filename", // set to null if not needed
            'type'         => 'upload',
            "disk"         =>config("save_disk.post_thumb"),
            "upload"       =>true,
            'aspect_ratio' => 1, // set to 0 to allow any aspect ratio
            'crop'         => true, // set to true to allow cropping, false to disable
            'src'          => NULL, // null to read straight from DB, otherwise set to model accessor function
        ]);
        CRUD::addField([
            'label'     => 'Published',
            'name'  => 'status',
            'type'  => 'checkbox',
            "default"=>"1",
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);
        CRUD::addField([
            'label'     => 'Allow Comments',
            'name'  => 'allow_comments',
            'type'  => 'checkbox',
            "default"=>"1",
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);

        CRUD::addField([
            "label"=>"User",
            "name"=>"user_id",
            "type"=>"hidden",
            "default"=>backpack_user()->id
        ]);
        CRUD::setValidation(PostRequest::class);
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
    protected function setupUpdateOperation()
    {
        $post = Post::where("id",$this->crud->getCurrentEntryId())->first();
        if(!backpack_user()->can("update",$post)){
            abort(403);
        }
        $this->setupCreateOperation();
    }
    public function fetchCategory()
    {
        return $this->fetch(\App\Models\Category::class);
    }
    public function fetchTag()
    {
        return $this->fetch(\App\Models\Tag::class);
    }
    public function tagOptions(Request $request) {
        $term = $request->input('term');
        $options = Tag::where('name', 'like', '%'.$term.'%')->get()->pluck('name', 'id');
        return $options;
    }
    public function categoryOptions(Request $request){
        $term = $request->input('term');
        $options = Category::where('name', 'like', '%'.$term.'%')->get()->pluck('name', 'id');
        return $options;
    }
}
