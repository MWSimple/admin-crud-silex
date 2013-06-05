# admin-crud-silex
### currently in development.

## Installation

### Using composer

Add following lines to your `composer.json` file:

### Silex 2.2
Using [fabpot/Silex-Skeleton](https://github.com/fabpot/Silex-Skeleton)

    "require": {
      ...
      "mwsimple/admin-crud-silex": "2.2.*@dev"
    }

Execute:

    php composer.phar update "mwsimple/admin-crud-orm-silex"

## Dependencies

Using [doctrine](http://silex.sensiolabs.org/doc/providers/doctrine.html)

## Usage

Add it to the `src/Controller/PostController.php` class:

	<?php
	namespace Controller;

	use MWSimple\Silex\AdminCrudSilex\CrudController;

	class PostController extends CrudController
	{
	}

Add it to the `src/app.php` class:

	use Silex\Application;
	...
    use Controller\PostController;
    ...
    //CONTROLLER POST
	$app['post.controller'] = $app->share(function() use ($app) {
	    $options = array(
        	'dirTemplate' => 'Post/',
        	'table' => 'post'
        	'route' => 'post'
	    );
	    return new PostController($app,$options);
	});

Add it to the `src/controllers.php` class:

	...
	use Symfony\Component\HttpFoundation\Request;

	Request::enableHttpMethodParameterOverride();
	...
	...
	//CONTROLLER POST
	$app->get('/post', "post.controller:indexAction")->bind('post');
	$app->post('/post/', "post.controller:createAction")->bind('post_create');
	$app->get('/post/new', "post.controller:newAction")->bind('post_new');
	$app->get('/post/{id}', "post.controller:showAction")->bind('post_show');
	$app->get('/post/{id}/edit', "post.controller:editAction")->bind('post_edit');
	$app->post('/post/{id}', "post.controller:updateAction")->bind('post_update')
	  ->method('PUT|POST')
	;
	$app->delete('/post/{id}', "post.controller:deleteAction")->bind('post_delete')
	  ->method('DELETE')
	;

Add it to the `src/Controller/ConfigController.php` class:

	<?php
	namespace Controller;

	abstract class ConfigController
	{
	    static function createForm($table, $app, $entity)
	    {
	        switch ($table) {
	//post
	            case 'post':
	                $form = $app['form.factory']->createBuilder('form', $entity)
	                    ->add('title')
	                    ->getForm()
	                ;
	                break;
	            default:
	                $form = null;
	                break;
	        }
	        return $form;
	    }

	    static function createList($table)
	    {
	        switch ($table) {
	//post
	            case 'post':
	                $list = array('id','title');
	                break;
	            default:
	                $list = array('id');
	                break;
	        }
	        return $list;
	    }

	    static function createShow($table)
	    {
	        switch ($table) {
	//post
	            case 'post':
	                $show = array('id','title','description', 'content');
	                break;
	            default:
	                $show = array('id');
	                break;
	        }
	        return $show;
	    }
	}

copy the contents of the folder `AdminCrudSilex/Resources/public/admin-crud-silex/*` to `web/admin-crud-silex/`

copy the contents of the folder `AdminCrudSilex/Resources/crudViews/*` to `templates/crudViews/`

## Author

Gonzalo Alonso - gonkpo@gmail.com
