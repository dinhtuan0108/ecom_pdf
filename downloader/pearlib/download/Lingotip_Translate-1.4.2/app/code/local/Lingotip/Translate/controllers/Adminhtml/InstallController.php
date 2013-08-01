<?php 
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Adminhtml_InstallController extends Mage_Adminhtml_Controller_action

{  

	protected function _initAction() {

 		$this->loadLayout()

			->_setActiveMenu('install/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Install Manager'), Mage::helper('adminhtml')->__('Install'));

		

		return $this;

	}   

 

	public function indexAction() {

		$this->_forward('edit');

	}



	public function editAction() {

		$model = Mage::getModel('translate/install');

		$model->getRegisterationData($model); //get the registeration data, if he/she is already registered

		Mage::register('install_data', $model); // set the data in registeration form , if client is already/newly registered

		$this->loadLayout();

		$this->_setActiveMenu('install/items');

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('translate/adminhtml_install_edit'))

			->_addLeft($this->getLayout()->createBlock('translate/adminhtml_install_edit_tabs'));

		$this->renderLayout(); 

	}

 

	public function newAction() {

		$this->_forward('edit');

	}

 

	public function saveAction() {

		if ($data = $this->getRequest()->getPost()) {

			$model = Mage::getModel('translate/install');

			if (!isset($data['terms_con']) || $data['terms_con'] != 1)

			{

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Terms and condition must be accepted"));

				$this->_redirect('*/*/');return;

			}



			$f_name =  $data['f_name'];

			$l_name =  $data['l_name'];

			$usermail =  $data['email'];

			$password =  $data['password']; 

			//$cpath = $data['cpath'];

			$cpath = Mage::getBaseUrl();

			if(strpos($cpath,"/index.php/")){

			

				$index = array("/index.php/");

				$cpath = str_replace($index, "", $cpath);

			}

 

			$cid  = Lingotip_Translate_Helper_Data::cid;

			

			$isUseAdminSecretKey = Mage::getStoreConfig('admin/url/use_custom');

			if($isUseAdminSecretKey != 0 && $isUseAdminSecretKey != "")

			{

				$adminSecretKey = Mage::getStoreConfig('admin/url/custom');

				$adminUrl = Mage::getBaseUrl().$adminSecretKey;

			}else{

				$adminSecretKey = 'admin/';

				$adminUrl = Mage::getBaseUrl().$adminSecretKey;

			}

			//$adminUrl = Mage::getBaseUrl()."translate/adminhtml_translate";
			

			//set parameter need to send to Lingotip Api

			$parameters="cid=".$cid."&u=".$usermail."&p=".$password."&f_name=".urlencode($f_name)."&l_name=".urlencode($l_name).

			"&cpath=".$cpath."&status=Reg"."&apath=".$adminUrl;

		

		 	$result = Mage::helper('translate')->installClient($parameters);

			//echo '<pre>'; print_r($result); die; // Provide feedback that registration is OK.

		

			$ltUrl = $result["LTURL"];

			$ltpp = $result["LTPP"];

		 

			if ($result["RC"] != "OK")

			{

				$RCError = $result["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in installation ".$RCError));

				$this->_redirect('*/*/');return;

			}

				

			try {

					//get the client installation data, if he is already registered

					$registerationData = $model->getRegisterationData($model); 

					//get the registeration installation if, if client is already registered

					$installId = $registerationData->getId(); 

					// delete the already registered client

					$model->setId($installId)->delete(); 



					$data['lturl'] = $ltUrl;

					$data['cid'] = $cid;

					$data['ltpp'] = $ltpp;



					

					$model->setData($data);

					$model->save();

					$languagePairData = array();

					

					//delete all the languages and insert new one

					Mage::getModel('translate/languagepair')->deleteAllLangauge();

					

		$lingotipLanguagePair = Mage::helper('translate')->getLanguagePairFromLingoTiP();

		if(isset($lingotipLanguagePair['RC']) && $lingotipLanguagePair['RC'] != "OK")

		{

			$RCError = $lingotipLanguagePair["RC"];

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Target Languages has not saved ".$RCError));

			$this->_redirect('*/*/');return;

		}

					

					$languageModel = Mage::getModel('translate/languagepair');

					foreach($lingotipLanguagePair as $key=>$val){

						$explodeArray = explode(":",$val);

						 

						if($explodeArray[0] != "OK"){

							$languagePairData['source'] = $explodeArray[0];

							$languagePairData['targetlan'] = $explodeArray[1];

							 //echo '<pre>';print_r($languagePairData);die;

							//Mage::getModel('translate/languagepair')->setData($languagePairData);

							$languageModel->setData($languagePairData);

							$languageModel->save();

							//Mage::getModel('translate/languagepair')->save($languagePairData['source'],$languagePairData['target']);

						}	 

					}	 

					

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('You are successfully registered with LingoTip'));

					Mage::getSingleton('adminhtml/session')->setFormData(false);

					$this->_redirect('*/*/');

					return;

				}

			

				catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

	}



}