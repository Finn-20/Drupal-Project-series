<?php

namespace Drupal\ai_attachment_media\Plugin\media\Source;

use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use Drupal\json_field\Plugin\Field\FieldType\JSONItem;
use Drupal\media\MediaTypeInterface;

/**
 * External Link media source.
 *
 * @see \Drupal\file\FileInterface
 *
 * @MediaSource(
 *   id = "external_link",
 *   label = @Translation("External Link"),
 *   description = @Translation("Use external website links."),
 *   allowed_field_types = {"link"},
 *   thumbnail_alt_metadata_attribute = "alt",
 *   default_thumbnail_filename = "no-thumbnail.png"
 * )
 */
class Link extends MediaSourceBase {

  /**
   * {@inheritdoc}
   */  
  public function getMetadataAttributes() {
    return [
      'title' => $this->t('Title'),
    ];
  }

  /**
   * {@inheritdoc}
   */  
  public function getMetadata(MediaInterface $media, $attribute_name) {
    return parent::getMetadata($media, $attribute_name);
  }

  /**
   * {@inheritdoc}
   */
  public function createSourceField(MediaTypeInterface $type) {
    return parent::createSourceField($type);
  }
}