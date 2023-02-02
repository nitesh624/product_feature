<?php

namespace Drupal\product_feature\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\product_feature\Response\QRDataResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Controller which generates the QRimage.
 */
class QRController extends ControllerBase {

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * QRController constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Request object to get request params.
   */
  public function __construct(RequestStack $request) {
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  /**
   * Return the response for external url.
   *
   */
  public function withUrl() {
    $externalUrl = $this->request->getCurrentRequest()->query->get('path');

    return new QRDataResponse($externalUrl);
  }

}
