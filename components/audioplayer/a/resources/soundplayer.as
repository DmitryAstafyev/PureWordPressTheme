package {
	/*References*/
	import flash.external.*;
	import flash.net.URLRequest;
	import flash.media.Sound;
	import flash.media.SoundChannel;
	import flash.media.SoundTransform;
	import flash.events.Event;
	import flash.events.ProgressEvent;
	import flash.utils.Timer;
	import flash.events.TimerEvent;
	/*Player*/
	public class SoundPlayer {
		private var JavaScriptsMethods	: Object 		= {	onLoad				: null, 
															onTrackEnded		: null,
															onLoadingProgress	: null,
															updateCurrentTime 	: null,
															updateDuration 		: null};
		private var sourceURLRequest	: URLRequest 	= null;
		private var sound				: Sound			= null;
		private var soundChannel		: SoundChannel	= null;
		private var volume 				: Number 		= 0.5;
		private var timer				: Timer;
		private var playing				: Boolean;
		public function SoundPlayer(){
			this.playing = false;
			this.JavaScriptsIncomeInit	();
			this.TimerInit				();
		}
		//=== EVENTS =================================================================
		private function attachMediaEvents(){
			sound.			addEventListener(Event.COMPLETE, 			MediaEventOnLoad			);
			sound.			addEventListener(ProgressEvent.PROGRESS, 	MediaEventOnProgress		);
		}
		private function dispatchMediaEvents(){
			if (sound != null){
				if(sound.hasEventListener(Event.COMPLETE) === true){
					sound.			removeEventListener(Event.COMPLETE, 			MediaEventOnLoad			);
					sound.			removeEventListener(ProgressEvent.PROGRESS, 	MediaEventOnProgress		);
				}
			}
		}
		private function attachOtherMediaEvents(){
			soundChannel.	addEventListener(Event.SOUND_COMPLETE, 		MediaEventOnSoundComplite	);
		}
		private function dispatchOtherMediaEvents(){
			if (sound != null){
				if(sound.hasEventListener(Event.COMPLETE) === true){
					soundChannel.	removeEventListener(Event.SOUND_COMPLETE, 		MediaEventOnSoundComplite	);
				}
			}
		}
		//=== EVENTS::HANDLES =================================================================
		private function MediaEventOnLoad(event:Event):void{
			JavaScriptsOutOnLoad(sound.url);			
			MediaEventOnUpdateDuration();
			soundChannel.	addEventListener(Event.SOUND_COMPLETE, 		MediaEventOnSoundComplite	);
		}
		private function MediaEventOnSoundComplite(event:Event):void{
			JavaScriptsOutOnTrackEnded(sound.url);
			TimerStop();
		}
		private function MediaEventOnProgress(event:ProgressEvent):void{
			JavaScriptsOutOnLoadingProgress(event.bytesLoaded / event.bytesTotal);
		}
		private function MediaEventOnUpdateCurrent(event:TimerEvent):void{
			JavaScriptsOutUpdateCurrentTime(getPosition(),(soundChannel.leftPeak + soundChannel.rightPeak)/2);
		}
		private function MediaEventOnUpdateDuration(){
			JavaScriptsOutUpdateDuration(getDuration());
		}
		//=== JAVASCRIPT OUTS =================================================================
		private function JavaScriptsOutOnLoad(src:String){
			if (JavaScriptsMethods['onLoad'] !== null){
				ExternalInterface.call(JavaScriptsMethods['onLoad'], src);
			}
		}
		private function JavaScriptsOutOnTrackEnded(src:String){
			if (JavaScriptsMethods['onTrackEnded'] !== null){
				ExternalInterface.call(JavaScriptsMethods['onTrackEnded'], src);
			}
		}
		private function JavaScriptsOutOnLoadingProgress(progress:Number){
			if (JavaScriptsMethods['onLoadingProgress'] !== null){
				ExternalInterface.call(JavaScriptsMethods['onLoadingProgress'], progress);
			}
		}
		private function JavaScriptsOutUpdateCurrentTime(current:int, peak:Number){
			if (JavaScriptsMethods['updateCurrentTime'] !== null){
				ExternalInterface.call(JavaScriptsMethods['updateCurrentTime'], current, peak);
			}
		}
		private function JavaScriptsOutUpdateDuration(duration:int){
			if (JavaScriptsMethods['updateDuration'] !== null){
				ExternalInterface.call(JavaScriptsMethods['updateDuration'], duration);
			}
		}
		//=== JAVASCRIPT INCOME =================================================================
		private function JavaScriptsIncomeInit(){
			ExternalInterface.addCallback("load", 				JavaScriptsIncomeLoad			);
			ExternalInterface.addCallback("play", 				JavaScriptsIncomePlay			);
			ExternalInterface.addCallback("stop", 				JavaScriptsIncomeStop			);
			ExternalInterface.addCallback("duration", 			JavaScriptsIncomeDuration		);
			ExternalInterface.addCallback("currentTime", 		JavaScriptsIncomeGetPosition	);
			ExternalInterface.addCallback("setVolume", 			JavaScriptsIncomeSetVolume		);
			ExternalInterface.addCallback("getVolume", 			JavaScriptsIncomeGetVolume		);
			ExternalInterface.addCallback("setPosition", 		JavaScriptsIncomeSetPosition	);
			ExternalInterface.addCallback("getPosition", 		JavaScriptsIncomeGetPosition	);
			ExternalInterface.addCallback("setEventHandle", 	JavaScriptsIncomeSetEventHandle	);
		}
		private function JavaScriptsIncomeLoad(url:String){
			load(url);
		}
		private function JavaScriptsIncomePlay(fromSecond:int){
			play(fromSecond);
		}
		private function JavaScriptsIncomeStop(){
			stop();
		}
		private function JavaScriptsIncomeDuration(){
			return getDuration();
		}
		private function JavaScriptsIncomeSetVolume(volume:Number){
			setVolume(volume);
		}
		private function JavaScriptsIncomeGetVolume(){
			return getVolume();
		}
		private function JavaScriptsIncomeSetPosition(fromSecond:int, volume:Number){
			setPosition(fromSecond, volume);
		}
		private function JavaScriptsIncomeGetPosition(){
			return getPosition();
		}
		private function JavaScriptsIncomeSetEventHandle(name:String, path:String){
			if (name in JavaScriptsMethods === true){
				JavaScriptsMethods[name] = path;
				return 1;
			}
			return 0;
		}
		//=== ACTIONS:: BASIC =================================================================
		private function load(url:String){
			dispatchMediaEvents();
				sourceURLRequest 	= new URLRequest	(url);
				sound 				= new Sound			();
				soundChannel 		= new SoundChannel	();
			attachMediaEvents();
			sound.load(sourceURLRequest);
		}
		private function play(fromSecond:int){
			if(sound.bytesLoaded > 0 && playing === false){
				soundChannel 	= this.sound.play(fromSecond * 1000);
				updateVolume	();
				TimerStart		();
				playing 		= true;
				attachOtherMediaEvents();
			}
		}
		private function stop(){
			if (playing === true){
				TimerStop();
				soundChannel.stop();
				playing = false;
				dispatchOtherMediaEvents();
			}
		}
		//=== ACTIONS:: POSITION AND DURATION =================================================================
		private function setPosition(fromSecond:int, volume:Number){
			if (playing === true){
				stop();
				play(fromSecond);
				setVolume(volume);
			}
		}
		private function getPosition(){
			return Math.floor(soundChannel.position / 1000 );
		}
		private function getDuration(){
			return Math.floor((sound.bytesTotal / (sound.bytesLoaded / sound.length)) / 1000);
		}
		//=== ACTIONS:: VOLUME =================================================================
		private function updateVolume(){
			var soundTransform:SoundTransform 	= new SoundTransform();
			soundTransform.volume 				= this.volume;
			soundChannel.soundTransform 		= soundTransform;
		}
		private function setVolume(volume:Number){
			this.volume = volume;
			updateVolume();
		}
		private function getVolume(){
			return this.volume;
		}
		//=== TIMER =================================================================
		private function TimerInit(){
			timer = new Timer(50);
			timer.addEventListener(TimerEvent.TIMER, MediaEventOnUpdateCurrent);
		}
		private function TimerStart(){
			timer.start();
		}
		private function TimerStop(){
			timer.stop();
		}		
	}
	
}
