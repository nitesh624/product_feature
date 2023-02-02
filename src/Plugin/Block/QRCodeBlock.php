<?php

namespace Drupal\product_feature\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\product_feature\Response\QRDataResponse;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'QRCodeBlock' block.
 *
 * @Block(
 *  id = "qrcode_block",
 *  admin_label = @Translation("Product qrcode block"),
 * )
 */
class QRCodeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->currentRouteMatch = $container->get('current_route_match');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }



  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');
    if ($node instanceof NodeInterface && $node->bundle() === 'product' && $node->hasField('field_app_purchase_link')) {
      $app_purchase_link = $node->field_app_purchase_link->uri;

      $request_data = UrlHelper::isValid($app_purchase_link);
      if ($request_data) {
        $option = [
          'query' => ['path' => $app_purchase_link],
        ];
        $uri = Url::fromRoute('product_feature.qrurl', [], $option)->toString();
      }
      $build = [];
      $build['#theme'] = 'image';
      $build['#uri'] = $uri;
      $build['#cache']['tags'] = $this->getCacheTags();
      $build['#cache']['contexts'] = $this->getCacheContexts();
    }
    return $build;
  }


  public function getCacheTags() {
    if ($node = $this->currentRouteMatch->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

}


