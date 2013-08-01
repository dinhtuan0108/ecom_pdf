<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Adminhtml_TranslateController extends Mage_Adminhtml_Controller_action

{ 

	protected function _initAction() 

	{ 

		$this->loadLayout()

			->_setActiveMenu('translate/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Translation Manager'), Mage::helper('adminhtml')->__('Translation Manager'));

		return $this;

	}   

 

	public function indexAction() {

		$this->_initAction()

			->renderLayout();

	}

	

	public function viewAction()

	{

		$this->loadLayout();

	

	    $this->_setActiveMenu('translate/items');

		  // $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

	// $this->_addContent($this->getLayout()->createBlock('translate/adminhtml_translate_view_tab_form'));

		

		$this->renderLayout();

 	}

	

	public function saveResponseAction()

	{

  

   		$id = $this->getRequest()->getParam('id');

		$paramArray = $this->getRequest()->getParams();

		//echo '<pre>';print_r($paramArray);die;

		$action = $paramArray['action']; 

		$note = $paramArray['note']; 

		

		if($action == "dispute")

		{

			$disTxn = $paramArray['txn'];

			$noOFDispute = Mage::getResourceModel('translate/dispute')->getCountDispute($disTxn);

			$countDispute = count($noOFDispute);

			if($countDispute > 1)
 			{
 				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Only two disputes are allowed - please contact support@lingotip.com."));

				$this->_redirect('*/*/index/');return;
			}
		}
		
		if($action == "feedback")
		{
			$feedbackTxn = $paramArray['txn'];
			$checkFeebackIsAlreadyDone = Mage::getResourceModel('translate/txn')->checkFeedbackDone($feedbackTxn );
			if($checkFeebackIsAlreadyDone == 1)
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__("Multiple feedback is not allowed."));
				$this->_redirect('*/*/index/');return;
			}
		}
		
		$result = Mage::helper('translate')->getXmlResult($level='',$source='',$t='',$text='',$action,'','','','','',$paramArray);

		if (isset($result["RC"]) && $result["RC"] != "OK")

		{

			$RCError = $result["RC"];

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in ".$action.": ".$RCError));

			$this->_redirect('*/*/index/');return;

		}

		try

		{

			if ($data = $this->getRequest()->getPost()) {
 
				$data['note'] = addslashes($data['note']);

				if($action == "answer")

				{

					$model = Mage::getModel('translate/notes');$model->setData($data);

					$model->save();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Message was sent.'));

					Mage::getSingleton('adminhtml/session')->setFormData(false);

					$this->_redirect('*/*/index/');

					return;

				}

				if($action == "dispute")

				{

					/*echo $data['txn'].'/';

					echo $disputeResource = Mage::getResourceModel('translate/dispute')->getdByTxn($data['txn']);

					die;*/

			

					$model = Mage::getModel('translate/dispute');

					$model->setData($data);

					$model->save();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Your dispute has been submitted.'));

				}

				

				if($action == "feedback")
				{
 					$feedTxn = $paramArray['txn'];
					 // update feedback = 1 when making feedback
					 $write = Mage::getSingleton('core/resource')->getConnection('core_write');
					 $write->query("UPDATE lt_txn SET feedback=1 WHERE txn=$feedTxn" );

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Your feedback has been submitted.'));
				}



				Mage::getSingleton('adminhtml/session')->setFormData(false);

				$this->_redirect('*/*/index/');

				return;

			}

		}

		catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/index');

                return;

         }

	}

	

	public function estimateAction()

	{

		$id     = $this->getRequest()->getParam('id');

		$model  = Mage::getModel('translate/translate')->load($id);

		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

  		 if ($model->getId()) {

  			if (!empty($data)) {

				$model->setData($data);

			}

 			Mage::register('translate_data', $model);

 			$this->loadLayout();

			$this->_setActiveMenu('translate/items');

 			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

  			$this->renderLayout();

		 }  

 	}



	public function postAction()

	{

		$id     = $this->getRequest()->getParam('id');

		$model  = Mage::getModel('translate/translate')->load($id);

		if ($model->getId() || $id == 0) {

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

 			if (!empty($data)) {

				$model->setData($data);

			}

			Mage::register('translate_data', $model);

			$this->loadLayout();

			$this->_setActiveMenu('translate/items');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

 			$this->renderLayout();

		}  

	

	}

	

	public function posteditAction()

	{

		$id     = $this->getRequest()->getParam('id');

		if(!isset($id) && $id == ""){ // Edit Case:  if we directly click on "Continue for Translation" the record does not save in translation(lt_estimate) table , so the id does not create , therefore we load the data from lt_request table 

			$rtxnid  = $this->getRequest()->getParam('rtxnid');

			$model  = Mage::getModel('translate/request')->load($rtxnid);

		}

		else{	// Edit Case : if we "Estimate Again", record insert in translate(lt_estimate) table , id generates and we load the data from  translate(lt_estimate) table 

				$model  = Mage::getModel('translate/translate')->load($id);

			}

		

		if ($model->getId() || $id == 0) {

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

 			if (!empty($data)) {

				$model->setData($data);

			}

			Mage::register('translate_data', $model);

			$this->loadLayout();

			$this->_setActiveMenu('translate/items');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

 			$this->renderLayout();

		}  

	

	}

	

	public function editAction() {

	

	$isedit = $this->getRequest()->getParam('isedit');

	$showblock = $this->getRequest()->getParam('showblock');

	

	if(isset($isedit) && $isedit != "" && $isedit == "yes" && !isset($showblock) && $showblock != "yes"){

		$id     = $this->getRequest()->getParam('rid'); // case when page comes from grid in edit mode

		$model  = Mage::getModel('translate/request')->load($id);

	}

	else{

			$id     = $this->getRequest()->getParam('id'); // case when page comes after submit estimate again

			$model  = Mage::getModel('translate/translate')->load($id);

	}		

		

 		if ($model->getId() || $id == 0) {

			if (!empty($data)) {

				$model->setData($data);

			}

 			Mage::register('translate_data', $model);

			$this->loadLayout();

			$this->_setActiveMenu('translate/items');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		//	$this->_addContent($this->getLayout()->createBlock('translate/adminhtml_translate_edit'));

			//$this->_addleft($this->getLayout()->createBlock('translate/adminhtml_translate_edit_tabs'));

 			$this->renderLayout();

		} 

	}

	

	public function paypalAction() {

		//$this->_forward('edit');

		 $id     = $this->getRequest()->getParam('id');

		 $model  = Mage::getModel('translate/request')->load($id);

		if ($model->getId() || $id == 0) {

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			if (!empty($data)) {

				$model->setData($data);

			}

			Mage::register('translate_data', $model);

			$this->loadLayout();

			$this->_setActiveMenu('translate/items');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->renderLayout();

		} 

	}

 

	public function newAction() {

 		 $id     = $this->getRequest()->getParam('id');

		 $model  = Mage::getModel('translate/translate')->load($id);



		if ($model->getId() || $id == 0) {

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			if (!empty($data)) {

				$model->setData($data);

			}

			Mage::register('translate_data', $model);

			$this->loadLayout();

			$this->_setActiveMenu('translate/items');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->renderLayout();

		} else {

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__('Item does not exist'));

			$this->_redirect('*/*/');

		}

		

	}

	

	public function targetLanguageAction() {

	$html= '';

	$source = $this->getRequest()->getParam('source');

	$getTargetLanguage  = Mage::getModel('translate/translate')->getTargetLanguages($source);

	

	$targetLanguageArray = explode(",",$getTargetLanguage);



$html .= '<select multiple="multiple" class=" required-entry select multiselect" size="10" name="language[]" id="trg_names">';	

	foreach($targetLanguageArray as $targetLanguage)

	{

 		$html .= '<option value="'.$targetLanguage.'">'.$targetLanguage.'</option>';

	}	

		$html .= '</select>';

		

		echo $html ; die;

 

	 }

 

	

	public function savePaypalAction() 

	{

  	 	//$requestId = $this->getRequest()->getParam('requestId');

		//$payment_status = $this->getRequest()->getParam('payment_status');

		//$data = array();

		//$data['paid'] = $payment_status ;

		 $model = Mage::getModel('translate/request');

		//$model->setData($data)->setId($requestId);$model->save(); 



		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('You have successfully paid for the translation.'));



		$this->_redirect('*/*/index');

		return;

	}

	public function savePostAction() {



		if ($data = $this->getRequest()->getPost()) {

		  //echo '<pre>';print_r($data );die;

			$id = $this->getRequest()->getParam('id');

			$model = Mage::getModel('translate/request');

			$modelTranslate = Mage::getModel('translate/translate')->load($id);

			$modelTxn = Mage::getModel('translate/txn');

 

			$captcha = $data['captcha']; $note = $data['note'];$dispatch_list = '';$targetNames = $data['trg_names'] ;

			$action = 'post';

			$request = Mage::helper('translate')->getXmlResult($data['level_id'],$data['src_name'],$targetNames,stripslashes($data['source']),$action,$captcha,$note,$dispatch_list);

			//echo '<pre>';print_r($request );die;

			if (isset($request["RC"]) && $request["RC"] != "OK")

			{

				$RCError = $request["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in translation: ".$RCError));

				$this->_redirect('*/*/post/id/'.$id);return;

			}

			$transactions = explode(',', $request["TXN"]);

			$code = $request["RAND"];

			$target = explode(',', $request["TARGET"]);

			$subtotal = explode(',', $modelTranslate->getSubtotal());

			$dataTxn = array();

			$data['txns'] = $request["TXN"] ;

			$data['price'] = $modelTranslate->getPrice();

			$data['word_count'] = $modelTranslate->getWordCount();

			$data['rid'] = $request["RID"] ;

			$makeVariable = $request["RID"];

			$model->setData($data);

			try {

					 $model->save();

					 $getLatestId =  $model->save()->getId();

					 

					 //LiongTip requst works oon rid not the autoincrement id

					 $write = Mage::getSingleton('core/resource')->getConnection('core_write');

					 $write->query("UPDATE lt_requests SET rid=$makeVariable WHERE request_id=$getLatestId" );

					 

					// for each request there are possible number of target languages which are seperate LT transactions

					$dataTxn['request_id'] = $model->getId();

					for ($i=0; $i<count($transactions); $i++) 

					{

						$dataTxn['txn'] 		= $transactions[$i]; 

						$dataTxn['code'] 		= $code ;

						$dataTxn['price'] 		= $subtotal[$i];

						$dataTxn['trg_name'] 	= $target[$i];

						$modelTxn->setData($dataTxn);

						$modelTxn->save();

					}

					Mage::getModel('translate/translate')->load($this->getRequest()->getParam('id'))->delete();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Please make your payment using the Buy Now button below or from the My Translations screen.'));

					Mage::getSingleton('adminhtml/session')->setFormData(false);



					$this->_redirect('*/*/paypal/id/'.$model->getId());

					return;

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/post', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__('Unable to find item to save'));

        $this->_redirect('*/*/');

	}

	

	public function savePosteditAction() {



//Edit case LT17

		if ($data = $this->getRequest()->getPost()) {

		  

 			$id = $this->getRequest()->getParam('id');

			$model = Mage::getModel('translate/request');

			

			if(!isset($id) && $id == "")

			{// Edit Case:  if we directly click on "Continue for Translation" the record does not save in translation(lt_estimate) table , so the id does not create , therefore we load the data from lt_request table 

				$id = $data['txn'] ;

				$modelTranslate = Mage::getModel('translate/request')->load($id);

			}else{// Edit Case : if we "Estimate Again", record insert in translate(lt_estimate) table , id generates and we load the data from  translate(lt_estimate) table 

				$modelTranslate = Mage::getModel('translate/translate')->load($id);

				 

 			}

			 

			$dispatch_list = '';$targetNames = $data['trg_names'] ;$txnId = $data['txn'] ;

			$dbrid = Mage::getModel('translate/request')->load($this->getRequest()->getParam('txn'))->getRid() ;

			

			$action = 'edit';

			$result = Mage::helper('translate')->getXmlResult($data['level_id'],$data['src_name'],$targetNames,stripslashes($data['source']),$action,'','','','',$dbrid);

 

			if (isset($result["RC"]) && $result["RC"] != "OK")

			{

				$RCError = $result["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in translation: ".$RCError));

				$this->_redirect('*/*/index');return;

			}



			$data['source'] = $modelTranslate->getSource();

			$data['word_count'] = $modelTranslate->getWordCount();

 

			$model->setData($data)->setId($this->getRequest()->getParam('txn'));

			

			try {

					$model->save();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Please make your payment using the Buy Now button below or from the My Translations screen.'));

					Mage::getSingleton('adminhtml/session')->setFormData(false);

					$this->_redirect('*/*/paypal/id/'.$txnId);

					//$this->_redirect('*/*/paypal/id/'.$model->getId());

					return;

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/post', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__('Unable to find item to save'));

        $this->_redirect('*/*/');

	}

	

	public function saveAction() {

	

		$formMode = $this->getRequest()->getParam('isAddMode');

		if ($data = $this->getRequest()->getPost()) {

 

			$model = Mage::getModel('translate/translate');

			$id = $this->getRequest()->getParam('id');

			$action = 'ESTIMATE';

			//echo '<pre>';print_r($data);die;

			if(isset($id) && $id != "" )

			{

				$targetNames = $data['trg_names']; // case : estimate again

			}

			else

			{

				$targetNames = implode(",",$data['language']); // case : new add

			}

			

			$result = Mage::helper('translate')->getXmlResult($data['level_id'],$data['src_name'],$targetNames,$data['source'],$action);



		  //echo '<pre>';print_r($result);die;

		  

		  if (!isset($result["RC"]))

			{

				$RCError = "RC is not set";

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in estimation: ".$RCError));

				$this->_redirect('*/*/index');return;

			}

			else if (isset($result["RC"]) && $result["RC"] != "OK")

			{

				$RCError = $result["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in estimation : ".$RCError));

				$this->_redirect('*/*/new');return;

			}



			$_SESSION['xml_return_array'] = $result;

	

			$ppword = explode(',', $result["PPWORD"]);

			$target = explode(',', $result["TARGET"]);

			$conversion = explode(',', $result["CONVERSION"]);		

			$word_count = $result["WORD_COUNT"];		

			$subtotal = explode(',', $result["TOTAL"]);		

			

			// calculate the total amount of translated text

			for($total=0,$i=0;$i<count($ppword);$i++)

			{

			 	$total += $subtotal[$i];

			}

			$data['word_count']  = $result["WORD_COUNT"];

			$data['subtotal']  = $result["TOTAL"];

			$data['paid']  =  'no';

			$data['src_name']  =  $result["SOURCE"];

			$data['trg_names'] = $result["TARGET"];	

			$data['level_id']  =	$result["LEVEL"];

			$data['price']  = $total;

			$data['source'] = addslashes($data['source']) ;



			$model->setData($data)->setId($this->getRequest()->getParam('id'));

 

			try {

				if(!isset($id) && $id == ""){Mage::getResourceModel('translate/translate')->deleteAll();}

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Estimate again or continue'));

				Mage::getSingleton('adminhtml/session')->setFormData(false);

				$id = $model->getId();

					$this->_redirect('*/*/estimate/id/'.$id);

					return;

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/new', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__('Unable to find item to save'));

        $this->_redirect('*/*/');

	}

	

	public function saveEditAction() {

 		$formMode = $this->getRequest()->getParam('isAddMode');

		$isedit = $this->getRequest()->getParam('isedit');

		$showblock = $this->getRequest()->getParam('showblock');

		

		if ($data = $this->getRequest()->getPost()) {

 			$model = Mage::getModel('translate/translate');

			$id = $this->getRequest()->getParam('id');

			$action = 'ESTIMATE';

			$targetNames = $data['trg_names']; // case : estimate again

			$result = Mage::helper('translate')->getXmlResult($data['level_id'],$data['src_name'],$targetNames,$data['source'],$action);



		  // echo '<pre>';print_r($result);die;

			if (!isset($result["RC"]))

			{

				$RCError = "RC is not set";

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in estimation: ".$RCError));

				$this->_redirect('*/*/index');return;

			}else if(isset($result["RC"]) && $result["RC"] != "OK")

			{

				$RCError = $result["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Error in estimation: ".$RCError));

				$this->_redirect('*/*/index');return;

			}



			$_SESSION['xml_return_array'] = $result;

	

			$ppword = explode(',', $result["PPWORD"]);

			$target = explode(',', $result["TARGET"]);

			$conversion = explode(',', $result["CONVERSION"]);		

			$word_count = $result["WORD_COUNT"];		

			$subtotal = explode(',', $result["TOTAL"]);		

			

			// calculate the total amount of translated text

			for($total=0,$i=0;$i<count($ppword);$i++)

			{

			 	$total += $subtotal[$i];

			}

			$data['word_count']  = $result["WORD_COUNT"];

			$data['subtotal']  = $result["TOTAL"];

			$data['paid']  =  'no';

			$data['src_name']  =  $result["SOURCE"];

			$data['trg_names'] = $result["TARGET"];	

			$data['level_id']  =	$result["LEVEL"];

			$data['price']  = $total;

			$data['source'] = addslashes($data['source']) ;

			

			if(isset($isedit) && $isedit != "" && $isedit == "yes" && !isset($showblock) && $showblock != "yes"){

				Mage::getResourceModel('translate/translate')->deleteAll(); // case when page comes from grid in edit mode

			}

			

			$model->setData($data)->setId($this->getRequest()->getParam('id'));



			try {



				$model->save();  

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('translate')->__('Estimate again or continue'));

				Mage::getSingleton('adminhtml/session')->setFormData(false);

				$id = $model->getId();

				$rtxnid = $this->getRequest()->getParam('rtxnid');

				$this->_redirect('*/*/edit/showblock/yes/id/'.$id."/rtxnid/".$rtxnid);

				return;

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('translate')->__('Unable to find item to save'));

        $this->_redirect('*/*/');

	}

   

	public function deleteAction() {

	 $id = $this->getRequest()->getParam('id');

	 $paidYesOrNo = Mage::getResourceModel('translate/request')->getPaymentStatusById($id); 

	 if($paidYesOrNo == "no")

	 {

		if( $this->getRequest()->getParam('id') > 0 ) {

			try {

				$model = Mage::getModel('translate/request');

				 

				$model->setId($this->getRequest()->getParam('id'))

					->delete();

					 

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Record was successfully deleted'));

				$this->_redirect('*/*/');

			} catch (Exception $e) {

				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

				$this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('id')));

			}

		}

	}else{	Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Paid Translation cannot be deleted.'));}

		$this->_redirect('*/*/');

	}



    public function massDeleteAction() {

        $flag = 0 ;

		$translateIds = $this->getRequest()->getParam('translate');

        if(!is_array($translateIds)) {

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));

        } else {

            try {

                foreach ($translateIds as $translateId) {

                    $translate = Mage::getModel('translate/request')->load($translateId);

					$paidYesOrNo = Mage::getResourceModel('translate/request')->getPaymentStatusById($translateId); 

					if($paidYesOrNo == "no"){

						$translate->delete();

					}else{$flag = 1;}

                }

				if($flag == 1){

				

				  Mage::getSingleton('adminhtml/session')->addError(

                    Mage::helper('adminhtml')->__(

                        'Paid requests cannot be deleted.'

                    )

                );

				

				}else{

				    

					Mage::getSingleton('adminhtml/session')->addSuccess(

                    Mage::helper('adminhtml')->__(

                        'Pending Translated record(s) were successfully deleted.'

                    ));

				}	

				

              

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

            }

        }

		

		

        $this->_redirect('*/*/index');

    }

 

}