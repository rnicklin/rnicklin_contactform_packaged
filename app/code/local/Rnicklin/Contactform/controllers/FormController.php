<?php

class Rnicklin_Contactform_FormController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

        if($this->getRequest()->isPost()) {

            $post = $this->getRequest()->getPost();

            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['phone']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig('contacts/email/email_template'),
                        'nicklin.robert@gmail.com',
                        'nicklin.robert@gmail.com',
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception('Mail could not be sent');
                }

                Mage::getSingleton('core/session')->addSuccess(Mage::helper('contactform')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                var_dump($e); exit;
                Mage::getSingleton('core/session')->addError(Mage::helper('contactform')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        }

        $this->loadLayout();
        $this->renderLayout();
    }
}