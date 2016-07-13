<?php
class Tiny_CompressImages_CompressImagesAdminhtml_StatusController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');

        return $session->isAllowed('admin/compressimages');
    }

    public function getApiStatusAction()
    {
        if (!$this->_validateFormKey()) {
            return;
        }

        $result = array();

        /** @var Tiny_CompressImages_Helper_Config $configHelper */
        $configHelper = Mage::helper('tiny_compressimages/config');
        $isConfigured = $configHelper->isConfigured();
        $apiKey = $configHelper->getApiKey();
        $isValidated = Mage::helper('tiny_compressimages/tinify')->validate($apiKey);

        $cacheData = array();
        if (false && $isConfigured && $isValidated) {
            $message = '<span class="compressimages_status_success">'
                . '<span class="indicator"><img src="' . Mage::getDesign()->getSkinUrl('images/fam_bullet_success.gif') . '"></span>'
                . Mage::helper('tiny_compressimages')->__('API connection successful')
                . '</span>';

            $cacheData['status'] = 'operational';
            Mage::app()->saveCache(json_encode($cacheData), 'tiny_compressimages_api_status');
        } else {
            $message = '<span class="compressimages_status_failure">'
                . '<span class="indicator"><img src="' . Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif') . '"></span>'
                . Mage::helper('tiny_compressimages')->__('Non-operational')
                . '</span>';

            $cacheData['status'] = 'nonoperational';
            Mage::app()->saveCache(json_encode($cacheData), 'tiny_compressimages_api_status');
        }

        $result['status'] = 'success';
        $result['message'] = $message;

        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        $this->getResponse()->setBody($coreHelper->jsonEncode($result));
    }
}
