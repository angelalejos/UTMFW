<?php 
/*
 * Copyright (C) 2004-2018 Soner Tari
 *
 * This file is part of UTMFW.
 *
 * UTMFW is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * UTMFW is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with UTMFW.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace View;

class Timeout extends Rule
{
	function display($ruleNumber, $count)
	{
		$this->dispHead($ruleNumber);
		$this->dispTimeout();
		$this->dispTail($ruleNumber, $count);
	}
	
	function dispTimeout()
	{
		?>
		<td title="<?php echo _TITLE('Timeout') ?>" colspan="12">
			<?php
			$this->arr= array();
			$this->dispTimeoutOpts();
			echo implode(', ', $this->arr);
			?>
		</td>
		<?php
	}

	function dispTimeoutOpts()
	{
		if (count($this->rule['timeout'])) {
			reset($this->rule['timeout']);
			while (list($timeout, $kvps)= each($this->rule['timeout'])) {	
				$timeout= $timeout == 'all' ? '' : "$timeout.";
				while (list($key, $val)= each($kvps)) {
					$this->arr[]= "$timeout$key: $val";
				}
			}
		}
	}

	function input()
	{
		$this->inputTimeoutOpt('frag', 'frag', 'all');
		$this->inputTimeoutOpt('interval', 'interval', 'all');

		$this->inputTimeout();

		$this->inputKey('comment');
		$this->inputDelEmpty();
	}

	function inputTimeout()
	{
		$this->inputTimeoutOpt('src.track', 'src_track', 'all');

		$this->inputTimeoutOpt('first', 'tcp_first', 'tcp');
		$this->inputTimeoutOpt('opening', 'tcp_opening', 'tcp');
		$this->inputTimeoutOpt('established', 'tcp_established', 'tcp');
		$this->inputTimeoutOpt('closing', 'tcp_closing', 'tcp');
		$this->inputTimeoutOpt('finwait', 'tcp_finwait', 'tcp');
		$this->inputTimeoutOpt('closed', 'tcp_closed', 'tcp');

		$this->inputTimeoutOpt('first', 'udp_first', 'udp');
		$this->inputTimeoutOpt('single', 'udp_single', 'udp');
		$this->inputTimeoutOpt('multiple', 'udp_multiple', 'udp');

		$this->inputTimeoutOpt('first', 'icmp_first', 'icmp');
		$this->inputTimeoutOpt('error', 'icmp_error', 'icmp');

		$this->inputTimeoutOpt('first', 'other_first', 'other');
		$this->inputTimeoutOpt('single', 'other_single', 'other');
		$this->inputTimeoutOpt('multiple', 'other_multiple', 'other');

		$this->inputTimeoutOpt('start', 'adaptive_start', 'adaptive');
		$this->inputTimeoutOpt('end', 'adaptive_end', 'adaptive');
	}

	function inputTimeoutOpt($key, $var, $parent)
	{
		if (filter_has_var(INPUT_POST, 'state')) {
			$this->rule['timeout'][$parent][$key]= trim(filter_input(INPUT_POST, $var), '" ');
		}
	}

	function edit($ruleNumber, $modified, $testResult, $generateResult, $action)
	{
		$this->editIndex= 0;
		$this->ruleNumber= $ruleNumber;

		$this->editHead($modified);

		$this->editFragment();
		$this->editInterval();

		$this->editTimeout();

		$this->editComment();
		$this->editTail($modified, $testResult, $generateResult, $action);
	}

	function editTimeout()
	{
		$this->editSrcTrack();
		$this->editTcpTimeouts();
		$this->editUdpTimeouts();
		$this->editIcmpTimeouts();
		$this->editOtherTimeouts();
		$this->editAdaptiveTimeouts();
	}

	function editFragment()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php echo _TITLE('Fragment').':' ?>
			</td>
			<td>
				<input type="text" id="frag" name="frag" size="10" value="<?php echo $this->rule['timeout']['all']['frag']; ?>" placeholder="<?php echo _CONTROL('number') ?>" />
				<?php $this->editHelp('frag') ?>
			</td>
		</tr>
		<?php
	}

	function editInterval()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php echo _TITLE('Interval').':' ?>
			</td>
			<td>
				<input type="text" id="interval" name="interval" size="10" value="<?php echo $this->rule['timeout']['all']['interval']; ?>" placeholder="<?php echo _CONTROL('number') ?>" />
				<?php $this->editHelp('interval') ?>
			</td>
		</tr>
		<?php
	}

	function editSrcTrack()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php echo _TITLE('Source Track Timeout').':' ?>
			</td>
			<td>
				<input type="text" id="src_track" name="src_track" size="10" value="<?php echo $this->rule['timeout']['all']['src.track']; ?>" placeholder="<?php echo _CONTROL('number') ?>" />
				<?php $this->editHelp('src.track') ?>
			</td>
		</tr>
		<?php
	}

	function editTcpTimeouts()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php $this->editHelp('tcp_timeout') ?><?php echo _TITLE('TCP Timeouts').':' ?>
			</td>
			<td>
				<table style="width: auto;">
					<?php
					$this->editTimeoutOpt('tcp', 'first');
					$this->editTimeoutOpt('tcp', 'opening');
					$this->editTimeoutOpt('tcp', 'established');
					$this->editTimeoutOpt('tcp', 'closing');
					$this->editTimeoutOpt('tcp', 'finwait');
					$this->editTimeoutOpt('tcp', 'closed');
					?>
				</table>
			</td>
		</tr>
		<?php
	}

	function editUdpTimeouts()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php $this->editHelp('udp_timeout') ?><?php echo _TITLE('UDP Timeouts').':' ?>
			</td>
			<td>
				<table style="width: auto;">
					<?php
					$this->editTimeoutOpt('udp', 'first');
					$this->editTimeoutOpt('udp', 'single');
					$this->editTimeoutOpt('udp', 'multiple');
					?>
				</table>
			</td>
		</tr>
		<?php
	}

	function editIcmpTimeouts()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php $this->editHelp('icmp_timeout') ?><?php echo _TITLE('ICMP Timeouts').':' ?>
			</td>
			<td>
				<table style="width: auto;">
					<?php
					$this->editTimeoutOpt('icmp', 'first');
					$this->editTimeoutOpt('icmp', 'error');
					?>
				</table>
			</td>
		</tr>
		<?php
	}

	function editOtherTimeouts()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php $this->editHelp('other_timeout') ?><?php echo _TITLE('Other Timeouts').':' ?>
			</td>
			<td>
				<table style="width: auto;">
					<?php
					$this->editTimeoutOpt('other', 'first');
					$this->editTimeoutOpt('other', 'single');
					$this->editTimeoutOpt('other', 'multiple');
					?>
				</table>
			</td>
		</tr>
		<?php
	}

	function editAdaptiveTimeouts()
	{
		?>
		<tr class="<?php echo ($this->editIndex++ % 2 ? 'evenline' : 'oddline'); ?>">
			<td class="title">
				<?php $this->editHelp('adaptive_timeout') ?><?php echo _TITLE('Adaptive Timeouts').':' ?>
			</td>
			<td>
				<table style="width: auto;">
					<?php
					$this->editTimeoutOpt('adaptive', 'start');
					$this->editTimeoutOpt('adaptive', 'end');
					?>
				</table>
			</td>
		</tr>
		<?php
	}

	function editTimeoutOpt($timeout, $key)
	{
		?>
		<tr>
			<td class="ifs">
				<input type="text" size="10" id="<?php echo $timeout ?>_<?php echo $key ?>" name="<?php echo $timeout ?>_<?php echo $key ?>" value="<?php echo $this->rule['timeout'][$timeout][$key]; ?>" placeholder="<?php echo _CONTROL('number') ?>" />
			</td>
			<td class="optitle"><?php echo $key ?></td>
		</tr>
		<?php
	}
}
?>
