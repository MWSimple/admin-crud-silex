<?php
namespace MWSimple\Silex\AdminCrudSilex;

use Symfony\Component\HttpFoundation\Request;
use Controller\ConfigController as configController;

class CrudController
{
    protected $app;
    protected $options;
    protected $class;

    public function __construct($app, $options)
    {
        $this->app = $app;
        $this->options = $options;
    }

    public function indexAction()
    {
        $sql = "SELECT * FROM ".$this->options['table'];
        $entities = $this->app['db']->fetchAll($sql);

        $list = configController::createList($this->options['table']);

        return $this->app['twig']->render($this->options['dirTemplate'].'index.html.twig', array(
            'entities' => $entities,
            'options' => $this->options,
            'campos' => $list
        ));
    }

    public function newAction()
    {
        $entity = new $this->class();
        $form = configController::createForm($this->options['table'], $this->app, $entity);
        // display the form
        return $this->app['twig']->render($this->options['dirTemplate'].'new.html.twig', array(
            'form' => $form->createView(),
            'options' => $this->options
        ));
    }

    public function createAction(Request $request)
    {
        $entity = new $this->class();
        $form = configController::createForm($this->options['table'], $this->app, $entity);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->app["orm.em"];
                $em->persist($entity);
                $em->flush();

                return $this->app->redirect($this->app['url_generator']->generate(
                    $this->options['route'].'_show', array('id' => $entity->getId())
                ));
            }
        }
        // display the form
        return $this->app['twig']->render($this->options['dirTemplate'].'new.html.twig', array(
            'form' => $form->createView(),
            'options' => $this->options
        ));
    }

    public function showAction($id)
    {
        $em = $this->app["orm.em"];

        $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

        if (!$entity) {
            $this->app->abort(404, $this->options['entityName']." $id does not exist.");
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->app['twig']->render($this->options['dirTemplate'].'show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'options' => $this->options
        ));
    }

    public function editAction($id)
    {
        $em = $this->app["orm.em"];

        $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

        if (!$entity) {
            $this->app->abort(404, $this->options['entityName']." $id does not exist.");
        }

        $editForm = configController::createForm($this->options['table'], $this->app, $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->app['twig']->render($this->options['dirTemplate'].'edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'options' => $this->options
        ));
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->app["orm.em"];

        $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

        if (!$entity) {
            $this->app->abort(404, $this->options['entityName']." $id does not exist.");
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = configController::createForm($this->options['table'], $this->app, $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->app->redirect($this->app['url_generator']->generate(
                $this->options['route'].'_edit', array('id' => $id)
            ));
        }

        return $this->app['twig']->render($this->options['dirTemplate'].'edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'options' => $this->options
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->app["orm.em"];
            $entity = $em->getRepository('Entity'.$this->options['entityRepo'])->find($id);

            if (!$entity) {
                $this->app->abort(404, $this->options['entityName']." $id does not exist.");
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->app->redirect($this->app['url_generator']->generate(
            $this->options['route']
        ));
    }

    protected function createDeleteForm($id)
    {
        return $this->app['form.factory']->createBuilder('form', array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}