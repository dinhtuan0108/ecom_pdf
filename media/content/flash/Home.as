package 
{
	import com.greensock.*;
	import com.greensock.easing.*;
	import flash.display.*;
	import flash.events.*;
	import flash.net.*;

	public class Home extends MovieClip
	{
		public function Home()
		{
			addEventListener(Event.ADDED_TO_STAGE, addedHandler);
			addEventListener(Event.REMOVED_FROM_STAGE, removedHandler);

			shadows.alpha = 0;
			padmaDeFleur_btn.alpha = 0;
			packages_btn.alpha = 0;
			collection_btn.alpha = 0;
			services_btn.alpha = 0;
			onlineOrder_btn.alpha = 0;
			
			box1.buttonMode = true;
			box2.buttonMode = true;
			box3.buttonMode = true;

			box1.boxText.mouseEnabled = false;
			box2.boxText.mouseEnabled = false;
			box3.boxText.mouseEnabled = false;

			box1.boxText.alpha = 0;
			box1.white2.scaleY = 0;
			box2.boxText.alpha = 0;
			box2.white2.scaleY = 0;
			box3.boxText.alpha = 0;
			box3.white2.scaleY = 0;

			//FLOWER TWEENS//
			TweenLite.from(flower1, 4, {delay:12, alpha:0});
			TweenLite.from(flower2, 4, {delay:12.7,  alpha:0});
			TweenLite.from(flower3, 4, {delay:13.4 , alpha:0});
			TweenLite.from(flower4, 4, {delay:14.1, alpha:0, 
							onComplete:moveForward});
			TweenLite.from(flower5, 2, {delay:15.8, alpha:0});
			

			//BG TWEENS//
			TweenLite.from(floral, 20, {alpha:0});

			//PIC_BUTTONS TWEENS//
			TweenLite.from(box1, 1.1, {delay:13.1, x:-337.05, alpha:0, 
						   onComplete:onEndBoxMove});
			TweenLite.from(box2, 1.1, {delay:12.7, x:-100.05, alpha:0});
			TweenLite.from(box3, 1.1, {delay:12.3, x:137.1, alpha:0});
		}
		
		function moveForward():void
		{
			addListeners();
			//SHADOW TWEENS//
			TweenLite.to(shadows, 2, {alpha:1});
			//NAVI TWEENS//;
			TweenLite.to(padmaDeFleur_btn, 2, {alpha:1});
			TweenLite.to(packages_btn, 2, {alpha:1});
			TweenLite.to(collection_btn, 2, {alpha:1});
			TweenLite.to(services_btn, 2, {alpha:1});
			TweenLite.to(onlineOrder_btn, 2, {alpha:1});
		}
		
		//PIC_BUTTONS-IMAGES//
		function onEndBoxMove()
		{
			box1.addEventListener(MouseEvent.ROLL_OVER, onBoxOver1);
			box1.addEventListener(MouseEvent.ROLL_OUT, onBoxOut1);
			box2.addEventListener(MouseEvent.ROLL_OVER, onBoxOver2);
			box2.addEventListener(MouseEvent.ROLL_OUT, onBoxOut2);
			box3.addEventListener(MouseEvent.ROLL_OVER, onBoxOver3);
			box3.addEventListener(MouseEvent.ROLL_OUT, onBoxOut3);
			
			box1.addEventListener(MouseEvent.CLICK, onBox1Click);
			box2.addEventListener(MouseEvent.CLICK, onBox2Click);
			box3.addEventListener(MouseEvent.CLICK, onBox3Click);

			function onBoxOver1(event:MouseEvent):void
			{
				TweenLite.to(box1.white, 1, {scaleX:2.2, alpha:0});
				TweenLite.to(box1.white2, .6, {delay:.5, scaleY:-0.7, alpha:1});
				TweenLite.to(box1.boxText, .4, {delay:.5,y:12, alpha:1});
			}
			function onBoxOut1(event:MouseEvent):void
			{
				TweenLite.to(box1.white, 0.5, {delay:.4,scaleX:1, alpha:0.6});
				TweenLite.to(box1.white2, .6, { scaleY:0, alpha:0});
				TweenLite.to(box1.boxText, .4, {y:-4.9, alpha:0});
			}
			
			function onBoxOver2(event:MouseEvent):void
			{
				TweenLite.to(box2.white, 1, {scaleX:2.2, alpha:0});
				TweenLite.to(box2.white2, .6, {delay:.5, scaleY:-0.7, alpha:1});
				TweenLite.to(box2.boxText, .4, {delay:.5,y:12, alpha:1});
			}
			function onBoxOut2(event:MouseEvent):void
			{
				TweenLite.to(box2.white, 0.5, {delay:.4,scaleX:1, alpha:0.6});
				TweenLite.to(box2.white2, .6, { scaleY:0, alpha:0});
				TweenLite.to(box2.boxText, .4, {y:-4.9, alpha:0});
			}

			function onBoxOver3(event:MouseEvent):void
			{
				TweenLite.to(box3.white, 1, {scaleX:2.2, alpha:0});
				TweenLite.to(box3.white2, .6, {delay:.5, scaleY:-0.7, alpha:1});
				TweenLite.to(box3.boxText, .4, {delay:.5,y:12, alpha:1});
			}
			function onBoxOut3(event:MouseEvent):void
			{
				TweenLite.to(box3.white, 0.5, {delay:.4,scaleX:1, alpha:0.6});
				TweenLite.to(box3.white2, .6, { scaleY:0, alpha:0});
				TweenLite.to(box3.boxText, .4, {y:-4.9, alpha:0});
			}
		}
				
		//MainNavigation Eventlistener//
		function addListeners():void
		{
			padmaDeFleur_btn.addEventListener(MouseEvent.CLICK, onPadmaDeFleurClick);
			padmaDeFleur_btn.addEventListener(MouseEvent.MOUSE_OVER,onPadmaDeFleurHover);
			padmaDeFleur_btn.addEventListener(MouseEvent.MOUSE_OUT, onPadmaDeFleurOut);

			packages_btn.addEventListener(MouseEvent.CLICK, onPackagesClick);
			packages_btn.addEventListener(MouseEvent.MOUSE_OVER, onPackagesHover);
			packages_btn.addEventListener(MouseEvent.MOUSE_OUT, onPackagesOut);

			collection_btn.addEventListener(MouseEvent.CLICK, onCollectionClick);
			collection_btn.addEventListener(MouseEvent.MOUSE_OVER, onCollectionHover);
			collection_btn.addEventListener(MouseEvent.MOUSE_OUT, onCollectionOut);

			services_btn.addEventListener(MouseEvent.CLICK, onServicesClick);
			services_btn.addEventListener(MouseEvent.MOUSE_OVER, onServicesHover);
			services_btn.addEventListener(MouseEvent.MOUSE_OUT, onServicesOut);

			onlineOrder_btn.addEventListener(MouseEvent.CLICK, onOnlineOrderClick);
			onlineOrder_btn.addEventListener(MouseEvent.MOUSE_OVER, onOnlineOrderHover);
			onlineOrder_btn.addEventListener(MouseEvent.MOUSE_OUT, onOnlineOrderOut);			
		}
		
		private function addedHandler(event:Event):void
		{
			this.x = stage.stageWidth / 2;
			this.y = stage.stageHeight / 2;
			stage.displayState = StageDisplayState.FULL_SCREEN;
		}
		private function removedHandler(event:Event):void
		{
		}
		
		//PicNavigation Funktionen//
		public function onBox1Click(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/collection/recycle-make-it-fashion-1.html"), "_self");
		}
		public function onBox2Click(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/collection/bouquet-1.html"), "_self");
		}
		public function onBox3Click(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/collection/creative-with-container-1.html"), "_self");
		}
		//MainNavigation Funktionen//
		public function onPadmaDeFleurClick(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/padmadefleur"), "_self");
		}
		public function onPackagesClick(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/packages"), "_self");
		}
		public function onServicesClick(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/services"), "_self");
		}
		public function onCollectionClick(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/collection.html"), "_self");
		}
		public function onOnlineOrderClick(event:MouseEvent):void
		{
			navigateToURL( new URLRequest
			("http://padmadefleur.vn/padma_vn/online_order"), "_self");
		}
		
		//Effects//
		public function onPadmaDeFleurHover(event:MouseEvent):void
		{
			addChild(flower1);
			flower1.gotoAndPlay(25);
		}
		public function onPadmaDeFleurOut(event:MouseEvent):void
		{
			flower1.gotoAndPlay(62);
		}
		public function onPackagesHover(event:MouseEvent):void
		{
			addChild(flower2);
			flower2.gotoAndPlay(2);
		}
		public function onPackagesOut(event:MouseEvent):void
		{
			flower2.gotoAndPlay(62);
		}
		public function onCollectionHover(event:MouseEvent):void
		{
			addChild(flower3);
			flower3.gotoAndPlay(2);
		}
		public function onCollectionOut(event:MouseEvent):void
		{
			flower3.gotoAndPlay(61);
		}
		public function onServicesHover(event:MouseEvent):void
		{
			addChild(flower4);
			flower4.gotoAndPlay(2);
		}
		public function onServicesOut(event:MouseEvent):void
		{
			flower4.gotoAndPlay(61);
		}
		public function onOnlineOrderHover(event:MouseEvent):void
		{
			addChild(flower5);
			flower5.gotoAndPlay(2);
		}
		public function onOnlineOrderOut(event:MouseEvent):void
		{
			flower5.gotoAndPlay(61);
		}
	}
}