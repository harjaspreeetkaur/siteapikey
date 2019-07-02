<?php

namespace Drupal\siteapikey\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SiteApiKeyController
 *   Returns a node as JSON.
 *
 * @package Drupal\siteapikey\Controller
 */
class SiteApiKeyController {

  /**
   * Checks access for a specific request.
   *
   * @param $apikey
   *   Key provided in the URL.
   * @param NodeInterface $node
   *   Node to return as JSON
   *
   * @return AccessResult
   *   Returns boolean for the access
   */
  public function accessCheck($apikey, NodeInterface $node) {
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    return AccessResult::allowedIf($this->validateNode($node) && $this->validateApiKey($apikey));
  }

  private function validateNode($node) {
    return $node->type->entity->label() == 'Custom Page';
  }

  /**
   * Validate provided API key with the configuration API saved
   *
   * @param $apikey
   *   The API key provided in the URL.
   *
   * @return bool
   */
  private function validateApiKey($apikey) {
    return $apikey === \Drupal::config('site.api')->get('key');
  }

  /**
   * Provide JSON of the node.
   *
   * @param $node
   *   Node object.
   *
   * @return JsonResponse
   */
  public function nodeToJson($node) {
    $serializer = \Drupal::service('serializer');
    $data = $serializer->serialize($node, 'json', ['plugin_id' => 'entity']);

    return new JsonResponse([
      'data' => $data
    ]);
  }

}
