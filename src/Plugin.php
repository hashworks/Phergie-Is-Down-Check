<?php

namespace hashworks\Phergie\Plugin\IsDownCheck;

use Phergie\Irc\Bot\React\AbstractPlugin;
use \WyriHaximus\Phergie\Plugin\Http\Request;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Plugin\React\Command\CommandEvent as Event;

/**
 * Plugin class.
 *
 * @category Phergie
 * @package hashworks\Phergie\Plugin\IsDownCheck
 */
class Plugin extends AbstractPlugin {

	/**
	 *
	 *
	 * @return array
	 */
	public function getSubscribedEvents () {
		return array(
				'command.isdown'      => 'handleCommand',
				'command.isdown.help' => 'handleCommandHelp',
		);
	}

	/**
	 * Sends reply messages.
	 *
	 * @param Event        $event
	 * @param Queue        $queue
	 * @param array|string $messages
	 */
	protected function sendReply (Event $event, Queue $queue, $messages) {
		$method = 'irc' . $event->getCommand();
		if (is_array($messages)) {
			$target = $event->getSource();
			foreach ($messages as $message) {
				$queue->$method($target, $message);
			}
		} else {
			$queue->$method($event->getSource(), $messages);
		}
	}

	public function handleCommand (Event $event, Queue $queue) {
		$site = $event->getCustomParams()[0];
		$this->sendReply($event, $queue, 'Checking ' . $site . '...');

		$errorHandler = function() use ($event, $queue, $site) {
			$this->sendReply($event, $queue, 'Failed to check online status of ' . $site. '.');
		};

		$url = 'http://downforeveryoneorjustme.com/' . rawurlencode($site);
		$this->emitter->emit('http.request', [new Request([
				'url'             => $url,
				'resolveCallback' => function ($data) use ($event, $queue, $site, $errorHandler) {
					if (preg_match("/<div id=\"container\">[\n\r\s]*(.*)[\n\r\s]*</", $data, $matches)) {
						$output = str_replace("  ", " ", strip_tags(html_entity_decode($matches[1])));
						if ($output == 'If you can see this page and still think we\'re down, it\'s just you.') {
							$output = 'If you can see this message and still think ' . $site . ' is down, it\'s just you.';
						}
						$this->sendReply($event, $queue, $output);
					} else {
						$errorHandler();
					}
				},
				'rejectCallback'  => $errorHandler
		])]);
	}

	/**
	 * Displays help information for the movie command.
	 *
	 * @param Event $event
	 * @param Queue $queue
	 */
	public function handleCommandHelp (Event $event, Queue $queue) {
		$this->sendReply($event, $queue, array(
				'Usage: isdown <host>',
				'Checks if a page is reachable using downforeveryoneorjustme.com.'
		));
	}

}
