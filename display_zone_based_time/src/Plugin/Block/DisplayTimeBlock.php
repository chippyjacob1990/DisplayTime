<?php

namespace Drupal\display_zone_based_time\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Display Time based on Timezone' block.
 *
 * @Block(
 *   id = "timezone_based_display_time_block",
 *   admin_label = @Translation("Display Time based on Timezone."),
 *   category = @Translation("Custom"),
 * )
 */
class DisplayTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs an DisplayTimeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DateFormatterInterface $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
    $configuration,
    $plugin_id,
    $plugin_definition,
    $container->get('date.formatter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('display_zone_based_time.timezonesettings');
    // Call a drupal service to get current time in specific format.
    $time = $this->dateFormatter->format(time(), 'custom', 'jS M Y - H:m A', $config->get('timezone'));

    $build['content'] = [
      '#theme' => 'display_zone_based_time',
      '#time' => $time,
      '#location' => $config->get('country') . "-" . $config->get('city'),
      '#cache' => [
        'expire' => time() + (60),
      ],
    ];
    return $build;
  }

}
