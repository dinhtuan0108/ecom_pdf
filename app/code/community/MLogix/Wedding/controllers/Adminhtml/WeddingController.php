<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

class MLogix_Wedding_Adminhtml_WeddingController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('wedding/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Wedding Manager'), Mage::helper('adminhtml')->__('Wedding Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
			
		$this->_initAction()
			->renderLayout();
		//$this->_forward('edit');
	}
	
	public function renderLayout($output = '') {		
		if($this->getRequest()->getParam('isAjax'))
		{
			$this->getLayout()->getBlock('root')->setTemplate('ajax.phtml');		
		}		
		else 
		{
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			//->setContainerCssClass('catalog-categories');
		}
		//$this->getLayout()->getBlock('root')->unsetChild('left');
		parent::renderLayout($output);
	}
	
	public function categoriesJsonAction() {
    	$treeBlock = $this->getLayout()->createBlock('wedding/adminhtml_tree');
    	
    	$node = $this->getRequest()->getParam('node');
    	if(!$node) $node = 0;
        	
		$this->getResponse()->setBody($treeBlock->getTreeJson($node));		
	}	
	
	public function moveAction() {
        $nodeId           = $this->getRequest()->getPost('id', false);
        $parentNodeId     = $this->getRequest()->getPost('pid', false);
        $prevNodeId       = $this->getRequest()->getPost('aid', false);
        $prevParentNodeId = $this->getRequest()->getPost('paid', false);

        $category  = Mage::getModel('wedding/wedding')->load($nodeId);
        $response = $category->move($parentNodeId, $prevNodeId);
        
	
        
        $this->getResponse()->setBody($response);
	}	

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('wedding/wedding')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			if($this->getRequest()->getParam('parent') && $parent = (int)$this->getRequest()->getParam('parent'))
				$model->setParentId($parent);	

			Mage::register('wedding_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('wedding/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wedding')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					$path = Mage::getBaseDir('media') . DS . 'wedding' . DS;
					
					$itemId = $this->getRequest()->getParam('id');
					if($itemId)
					{
						$temporaryModel = Mage::getModel('wedding/wedding')->load($itemId);
						$oldFile = $temporaryModel->getFilename();
						if($oldFile && $oldFile != '' && file_exists($path.$oldFile))
							unlink($path.$oldFile);
					}
					
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					
					
					$destFile = $path . $_FILES['filename']['name'];
					$filename = $uploader->getNewFileName($destFile);
					
					$uploader->save($path, $filename );
					
					
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $filename;
			}
	  			
	  			
			$model = Mage::getModel('wedding/wedding');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));				
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				
				if(isset($_FILES['filename']))
					$model->makeThumbnail(150,120,true);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wedding')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wedding')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('wedding/wedding');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $weddingIds = $this->getRequest()->getParam('wedding');
        if(!is_array($weddingIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($weddingIds as $weddingId) {
                    $wedding = Mage::getModel('wedding/wedding')->load($weddingId);
                    $wedding->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($weddingIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $weddingIds = $this->getRequest()->getParam('wedding');
        if(!is_array($weddingIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($weddingIds as $weddingId) {
                    $wedding = Mage::getSingleton('wedding/wedding')
                        ->load($weddingId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($weddingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'wedding.csv';
        $content    = $this->getLayout()->createBlock('wedding/adminhtml_wedding_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'wedding.xml';
        $content    = $this->getLayout()->createBlock('wedding/adminhtml_wedding_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}