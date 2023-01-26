<?php

namespace Drupal\aller_restful_api\Plugin\rest\resource;

use Drupal\Core\Database\Database;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Provides node content along with content paragraphs for the frontend.
 *
 * @RestResource(
 *   id = "aller_content",
 *   label = @Translation("Drupal node along with content paragraphs"),
 *   uri_paths = {
 *     "canonical" = "api/content/{id}"
 *   }
 * )
 */
class AllerRestApiContentResource extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * Returns Node content for the frontend
   *
   * @param int $id
   *   The ID of the content node.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing node data including paragraphs
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the Node was not found.
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   *   Thrown when no Node ID was provided.
   */
  public function get($id = NULL) {
    $serializer = \Drupal::service('serializer');
    
    if ($id) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($id);
      if ($node) {
        $normalized = $this->normalize($node);
        $normalized['content'] = $this->normalizeParagraphs($node->content);
        return new ResourceResponse($normalized);
      }

      throw new NotFoundHttpException("Node with ID '$id' was not found");
    }

    throw new BadRequestHttpException('No node ID was provided');
  }

  /**
   * Normalize and process node content paragraphs. 
   * @param array $paragraphs
   *    The content paragraphs to be serialized. Typically Node->content
   * @return array
   *    An array of serialized paragraphs
   */
  protected function normalizeParagraphs($paragraphs) {
    $content = [];
    foreach ($paragraphs as $p) {
      $content[] = $this->normalize($p->entity);
    }
    
    return $content;
  }

  /**
   * Serialize stuff and run post processor.
   * @return array
   *    Assoc array with normalized and post processed data.
   */
  protected function normalize($object) {
    $serializer = \Drupal::service('serializer');
    $normalized = $serializer->normalize($object);
    return $this->postProcess($normalized);
  }

  /**
   * Simplify the structure of the returned data. 
   * Load media urls etc.
   * @param array $data data to be processed.
   * @return array Returns the data with a simplified structure and with any media fields embedded.
   */
  protected function postProcess($data) {
    $pretty = [];
    foreach ($data as $k => $v) {
      $pretty[$k] = $v[0]['value'] ?? $v;
      
      if ($v[0]['target_type'] == 'media') {
        $media = [];
        foreach ($v as $m) {
          $med = Media::load($m['target_id']);
          $media[] = $this->normalize($med);
        }
        $pretty[$k] = $media;
      }
    }

    return $pretty;
  }
}
