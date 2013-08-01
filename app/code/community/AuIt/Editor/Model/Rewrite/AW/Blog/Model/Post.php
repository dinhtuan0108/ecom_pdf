<?php
class AuIt_Editor_Model_Rewrite_AW_Blog_Model_Post extends AW_Blog_Model_Post{

	public function getShortContent(){
		$content = $this->getData('short_content');
		return Mage::Helper('auit_editor')->translateDirective($content);
	}

	public function getPostContent(){
		$content = $this->getData('post_content');
		return Mage::Helper('auit_editor')->translateDirective($content);
	}
}
