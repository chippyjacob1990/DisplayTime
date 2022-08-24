<?php

namespace Drupal\display_zone_based_time\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the country and timezone selection form.
 *
 * @internal
 */
class TimezoneSettingsForm extends ConfigFormBase {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(DateFormatterInterface $date_formatter) {
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'display_zone_based_time.timezonesettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'timezone_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('display_zone_based_time.timezonesettings');
    // Call a drupal service to get current time in specific format.
    $time = $this->dateFormatter->format(time(), 'custom', 'jS M Y - H:m A', $config->get('timezone'));

    $form['country'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Country'),
      '#default_value' => empty($config->get('country')) ? '' : $config->get('country'),
    ];

    $form['city'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('City'),
      '#default_value' => empty($config->get('city')) ? '' : $config->get('city'),
    ];

    $options = [
      "" => $this->t("--Select--"),
      "America/Chicago" => "America/Chicago",
      "America/Chicago" => "America/Chicago",
      "America/New_York" => "America/New_York",
      "Asia/Tokyo" => "Asia/Tokyo",
      "Asia/Dubai" => "Asia/Dubai",
      "Asia/Kolkata" => "Asia/Kolkata",
      "Europe/Amsterdam" => "Europe/Amsterdam",
      "Europe/Oslo" => "Europe/Oslo",
      "Europe/London" => "Europe/London",
    ];

    $form['timezone'] = [
      '#type' => 'select',
      // '#required' => TRUE,
      '#title' => $this->t('Timezone'),
      '#options' => $options,
      '#default_value' => $config->get('timezone'),
    ];

    $timezone = empty($config->get('timezone')) ? '' : $config->get('timezone');
    // Call a drupal service to get current time in specific format.
    if (!empty($timezone)) {
      $time = $this->dateFormatter->format(time(), 'custom', 'jS M Y - H:m A', $timezone);
      $form['time'] = [
        '#type' => 'markup',
        '#markup' => '<div class="display_time">' . $this->t('Current time based on the selected timezone :') . " " . $time . '</div>',
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('display_zone_based_time.timezonesettings')
      ->set('country', $form_state->getValue('country'))
      ->set('city', $form_state->getValue('city'))
      ->set('timezone', $form_state->getValue('timezone'))
      ->save();
  }

}
