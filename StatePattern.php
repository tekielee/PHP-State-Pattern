<?php
class YearSetupState {
	function __construct(object $clockSetup) {
		$this->clockSetup = $clockSetup;
		$this->year = date('Y');
	}
	
	public function previousValue(): int {
		return $this->year--;
	}
	
	public function nextValue(): int {
		return $this->year--;
	}
	
	public function selectValue(): void {
		$this->clockSetup->setState($this->clockSetup->getMonthSetupState());
	}
	
	public function getInstructions(): string {
		return 'Please set the year...';
	}
	
	public function getSelectedValue(): int {
		return $this->year;
	}	
}

class MonthSetupState {
	function __construct(object $clockSetup) {
		$this->clockSetup = $clockSetup;
		$this->month = date('m');
	}
	
	public function previousValue(): int {
		return $this->month--;
	}
	
	public function nextValue(): int {
		return $this->month++;
	}
	
	public function selectValue(): void {
		$this->clockSetup->setState($this->clockSetup->getDaySetupState());
	}
	
	public function getInstructions(): string {
		return 'Please set the month...';
	}
	
	public function getSelectedValue(): int {
		return $this->month === 13 ? 1 : $this->month;
	}	
}

class DaySetupState {
	function __construct(object $clockSetup) {
		$this->clockSetup = $clockSetup;
		$this->day = date('d');
	}
	
	public function previousValue(): int {
		return $this->day--;
	}
	
	public function nextValue(): int {
		return $this->day++;
	}
	
	public function selectValue(): void {
		$this->clockSetup->setState($this->clockSetup->getHourSetupState());
	}
	
	public function getInstructions(): string {
		return 'Please set the day...';
	}
	
	public function getSelectedValue(): int {
		if(date('n') === 2) {
			return $this->day === 29 ? 1 : $this->day;
		} else if(date('n') === 4 || date('n') === 6 || date('n') === 9 || date('n') === 11) {
			return $this->day === 31 ? 1 : $this->day;
		} else {
			return $this->day === 32 ? 1 : $this->day;
		}		
	}	
}

class HourSetupState {
	function __construct(object $clockSetup) {
		$this->clockSetup = $clockSetup;
		$this->hour = date('g');
	}
	
	public function previousValue(): int {
		return $this->hour--;
	}
	
	public function nextValue(): int {
		return $this->hour++;
	}
	
	public function selectValue(): void {
		$this->clockSetup->setState($this->clockSetup->getMinuteSetupState());
	}
	
	public function getInstructions(): string {
		return 'Please set the hour...';
	}
	
	public function getSelectedValue(): int {
		return $this->hour === 25 ? 1 : $this->hour;
	}	
}

class MinuteSetupState {
	function __construct(object $clockSetup) {
		$this->clockSetup = $clockSetup;
		$this->minute = date('i');
	}
	
	public function previousValue(): int {
		return $this->minute--;
	}
	
	public function nextValue(): int {
		return $this->minute++;
	}
	
	public function selectValue(): void {
		$this->clockSetup->setState($this->clockSetup->getFinishedSetupState());
	}
	
	public function getInstructions(): string {
		return 'Please set the minute...';
	}
	
	public function getSelectedValue(): int {
		return $this->minute === 61 ? 1 : $this->minute;
	}	
}

class FinishedSetupState {
	function __construct(object $clockSetup) {
		$this->clockSetup = $clockSetup;
	}
	
	public function previousValue(): void {
		// No op
	}
	
	public function nextValue(): void {
		// No op
	}
	
	public function selectValue(): void {
		// No op
	}
	
	public function getInstructions(): string {
		return 'Please knob to view selected date...';
	}
	
	public function getSelectedValue(): void {
		// No op
	}	
}

class ClockSetup {
	function __construct() {
		$this->yearState = new YearSetupState($this);
		$this->monthState = new MonthSetupState($this);
		$this->dayState = new DaySetupState($this);
		$this->hourState = new HourSetupState($this);
		$this->minuteState = new MinuteSetupState($this);
		$this->finishedState = new FinishedSetupState($this);
		$this->currentState = null;
		
		$this->setState($this->yearState);
	}
	
	public function setState(object $state): void {
		$this->currentState = $state;
	}
	
	public function rotateKnobLeft(): void {
		$this->currentState->previousValue();
	}
	
	public function rotateKnobRight(): void {
		$this->currentState->nextValue();
	}
	
	public function pushKnob(): void {
		$this->currentState->selectValue();
	}
	
	public function getYearSetupState(): object {
		return $this->yearState;
	}
	
	public function getMonthSetupState(): object {
		return $this->monthState;
	}
	
	public function getDaySetupState(): object {
		return $this->dayState;
	}
	
	public function getHourSetupState(): object {
		return $this->hourState;
	}
	
	public function getMinuteSetupState(): object {
		return $this->minuteState;
	}
	
	public function getFinishedSetupState(): object {
		return $this->finishedState;
	}
	
	public function getSelectedDate() {
		$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		$date = $this->yearState->getSelectedValue() . '-' . $this->monthState->getSelectedValue() . '-' . $this->dayState->getSelectedValue();

		return $this->yearState->getSelectedValue() . ' ' . $months[$this->monthState->getSelectedValue() - 1] . ' ' . $this->dayState->getSelectedValue() . ' ' . date('l', strtotime($date)) . ' ' . $this->hourState->getSelectedValue() . ':' . $this->minuteState->getSelectedValue();
	}
}

$clockSetup = new ClockSetup();
// Setup starts in 'year' state
$clockSetup->rotateKnobRight();
$clockSetup->pushKnob(); // 1 year on
$clockSetup->rotateKnobRight();
$clockSetup->rotateKnobRight();
$clockSetup->pushKnob(); // 2 months on

$clockSetup->rotateKnobRight();
$clockSetup->rotateKnobRight();
$clockSetup->pushKnob(); // 3 days on

$clockSetup->rotateKnobLeft();
$clockSetup->rotateKnobLeft();
$clockSetup->pushKnob(); // 2 hours previous on

$clockSetup->rotateKnobRight();
$clockSetup->pushKnob(); // 1 minute on

$clockSetup->pushKnob(); // finished state

echo $clockSetup->getSelectedDate();
?>