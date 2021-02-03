<?php

namespace Drupal\Tests\layout_builder_restrictions_by_region\FunctionalJavascript;

use Drupal\block_content\Entity\BlockContent;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Demonstrate that blocks can be individually restricted.
 *
 * @group layout_builder_restrictions_by_region
 */
class BlockPlacementWhitelistTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'block',
    'layout_builder',
    'layout_builder_restrictions',
    'layout_builder_restrictions_by_region',
    'node',
    'field_ui',
    'block_content',
  ];

  /**
   * Specify the theme to be used in testing.
   *
   * @var string
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create a node bundle.
    $this->createContentType(['type' => 'bundle_with_section_field']);

    $this->drupalLogin($this->drupalCreateUser([
      'access administration pages',
      'administer blocks',
      'administer node display',
      'administer node fields',
      'configure any layout',
      'configure layout builder restrictions',
      'create and edit custom blocks',
    ]));

    // Enable entity_view_mode_restriction_by_region plugin.
    // Disable entity_view_mode_restriction plugin.
    $layout_builder_restrictions_plugins = [
      'entity_view_mode_restriction' => [
        'weight' => 1,
        'enabled' => FALSE,
      ],
      'entity_view_mode_restriction_by_region' => [
        'weight' => 0,
        'enabled' => TRUE,
      ],
    ];
    $config = \Drupal::service('config.factory')->getEditable('layout_builder_restrictions.plugins');
    $config->set('plugin_config', $layout_builder_restrictions_plugins)->save();
  }

  /**
   * Verify that both tempstore and config storage function correctly.
   */
  public function testBlockRestrictionStorage() {
    $this->blockTestSetup();

    $this->getSession()->resizeWindow(1200, 4000);
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $field_ui_prefix = 'admin/structure/types/manage/bundle_with_section_field';

    // From the manage display page, go to manage the layout.
    $this->drupalGet("$field_ui_prefix/display/default");
    // Checking is_enable will show allow_custom.
    $page->checkField('layout[enabled]');
    $page->checkField('layout[allow_custom]');
    $page->pressButton('Save');
    $assert_session->linkExists('Manage layout');

    // Only allow two-column layout.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layout-restriction-restricted"]');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layouts-layout-twocol-section"]');
    $element->click();

    // Verify form behavior when restriction is applied to all regions.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $assert_session->checkboxChecked('edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-all');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]', 'All regions');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]', 'Unrestricted');

    // Verify form behavior when restriction is applied on a per-region basis.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-per-region"]');
    $element->click();
    $assert_session->checkboxChecked('edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-per-region');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]', 'First');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]', 'Unrestricted');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]', 'Second');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]', 'Unrestricted');

    // Test temporary storage.
    // Add restriction to First region.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->checkboxChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxNotChecked('Allow specific Content fields blocks:');

    // Restrict all 'Content' fields from options.
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-allowed-blocks-content-fields-restriction-whitelisted--")]');
    $element->click();
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    // Verify First region is 'Restricted' and Second region
    // remains 'Unrestricted'.
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]', 'Restricted');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]', 'Unrestricted');

    // Reload First region allowed block form to verify temp storage.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->checkboxNotChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxChecked('Allow specific Content fields blocks:');
    $page->pressButton('Close');

    // Load Second region allowed block form to verify temp storage.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->checkboxChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxNotChecked('Allow specific Content fields blocks:');
    $page->pressButton('Close');

    // Verify All Regions unaffected.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-all"]');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]', 'Unrestricted');
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->checkboxChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxNotChecked('Allow specific Content fields blocks:');
    $page->pressButton('Close');

    // Switch back to Per-region restrictions prior to saving.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-per-region"]');
    $element->click();

    // Allow one-column layout.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layouts-layout-onecol"]');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-onecol"]/summary');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-onecol-table"]/tbody/tr[@data-region="all_regions"]', 'Unrestricted');
    // Save to config.
    $page->pressButton('Save');

    // Verify no block restrictions bleed to other layouts/regions upon save
    // to database.
    $this->drupalGet("$field_ui_prefix/display/default");
    // Check two-column layout.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]', 'Restricted');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]', 'Unrestricted');

    // Verify All Regions unaffected.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-all"]');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]', 'Unrestricted');

    // Check one-column layout.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-onecol"]/summary');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-onecol-table"]/tbody/tr[@data-region="all_regions"]', 'Unrestricted');
  }

  /**
   * Verify that the UI can restrict blocks in Layout Builder settings tray.
   */
  public function testBlockRestriction() {
    $this->blockTestSetup();

    $this->getSession()->resizeWindow(1200, 4000);
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $field_ui_prefix = 'admin/structure/types/manage/bundle_with_section_field';

    // From the manage display page, go to manage the layout.
    $this->drupalGet("$field_ui_prefix/display/default");
    // Checking is_enable will show allow_custom.
    $page->checkField('layout[enabled]');
    $page->checkField('layout[allow_custom]');
    $page->pressButton('Save');
    $assert_session->linkExists('Manage layout');

    // Only allow two-column layout.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layout-restriction-restricted"]');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layouts-layout-twocol-section"]');
    $element->click();

    // Switch to per-region restriction.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-per-region"]');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]', 'Restricted');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]', 'Unrestricted');
    $page->pressButton('Save');

    $this->clickLink('Manage layout');
    // Remove default one-column layout and replace with two-column layout.
    $this->clickLink('Remove Section 1');
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Remove');
    $assert_session->assertWaitOnAjaxRequest();
    $this->clickLink('Add section');
    $assert_session->assertWaitOnAjaxRequest();
    $this->clickLink('Two column');
    $assert_session->assertWaitOnAjaxRequest();
    $element = $page->find('xpath', '//*[contains(@class, "ui-dialog-off-canvas")]//*[starts-with(@id,"edit-actions-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    // Select 'Add block' link in First region.
    $element = $page->find('xpath', '//*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    // Initially, the body field is available.
    $assert_session->linkExists('Body');
    // Initially, custom blocks instances are available.
    $assert_session->linkExists('Basic Block 1');
    $assert_session->linkExists('Basic Block 2');
    $assert_session->linkExists('Alternate Block 1');
    // Initially, all inline block types are allowed.
    $this->clickLink('Create custom block');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Basic');
    $assert_session->linkExists('Alternate');
    $page->pressButton('Close');
    $page->pressButton('Save');

    // Load Allowed Blocks form for First region.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-per-region"]');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    // Impose Custom Block type restrictions.
    $assert_session->checkboxChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxNotChecked('Allow specific Content fields blocks:');
    $assert_session->checkboxChecked('Allow all existing & new Custom block types blocks.');
    $assert_session->checkboxNotChecked('Allow specific Custom block types blocks:');

    // Restrict all 'Content' fields from options.
    $element = $page->find('xpath', '//*[contains(@class, "form-item-allowed-blocks-content-fields-restriction")]/input[@value="whitelisted"]');
    $element->click();
    // Restrict all Custom block types from options.
    $element = $page->find('xpath', '//*[contains(@class, "form-item-allowed-blocks-custom-block-types-restriction")]/input[@value="whitelisted"]');
    $element->click();
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");

    // Select 'Add block' link in First region.
    $element = $page->find('xpath', '//*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkNotExists('Body');
    $assert_session->linkNotExists('Basic Block 1');
    $assert_session->linkNotExists('Basic Block 2');
    $assert_session->linkNotExists('Alternate Block 1');
    // Inline block types are still allowed.
    $this->clickLink('Create custom block');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Basic');
    $assert_session->linkExists('Alternate');

    // Impose Inline Block type restrictions.
    $this->drupalGet("$field_ui_prefix/display/default");
    // Load Allowed Blocks form for First region.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    $assert_session->checkboxChecked('Allow specific Content fields blocks:');
    $assert_session->checkboxNotChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxChecked('Allow all existing & new Inline blocks blocks.');
    $assert_session->checkboxNotChecked('Allow specific Inline blocks blocks:');

    // Restrict all Inline blocks from options.
    $element = $page->find('xpath', '//*[starts-with(@id, "edit-allowed-blocks-inline-blocks-restriction-whitelisted--")]');
    $element->click();
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    // Check independent restrictions on Custom block and Inline blocks.
    $this->drupalGet("$field_ui_prefix/display/default");
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");

    $element = $page->find('xpath', '//*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    $assert_session->linkNotExists('Body');
    $assert_session->linkNotExists('Basic Block 1');
    $assert_session->linkNotExists('Basic Block 2');
    $assert_session->linkNotExists('Alternate Block 1');
    // Inline block types are not longer allowed.
    $assert_session->linkNotExists('Create custom block');

    // Whitelist some blocks / block types.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->checkboxChecked('Allow specific Content fields blocks:');

    // Allow only 'body' field as an option.
    $page->checkField('allowed_blocks[Content fields][allowed_blocks][field_block:node:bundle_with_section_field:body]');
    // Whitelist all "basic" Custom block types.
    $page->checkField('allowed_blocks[Custom block types][allowed_blocks][basic]');
    // Whitelist "alternate" Inline block type.
    $page->checkField('allowed_blocks[Inline blocks][allowed_blocks][inline_block:alternate]');

    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    $this->drupalGet("$field_ui_prefix/display/default");
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");
    $this->clickLink('Add block');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Body');
    // ... but other 'content' fields aren't.
    $assert_session->linkNotExists('Promoted to front page');
    $assert_session->linkNotExists('Sticky at top of lists');
    // "Basic" Custom blocks are allowed.
    $assert_session->linkExists('Basic Block 1');
    $assert_session->linkExists('Basic Block 2');
    // ... but "alternate" Custom blocks are disallowed.
    $assert_session->linkNotExists('Alternate Block 1');
    // Only the basic inline block type is allowed.
    $this->clickLink('Create custom block');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkNotExists('Basic');
    $assert_session->linkExists('Alternate');

    // Custom block instances take precedence over custom block type setting.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    $element = $page->find('xpath', '//*[starts-with(@id, "edit-allowed-blocks-custom-blocks-restriction-whitelisted--")]');
    $element->click();
    // Allow Alternate Block 1.
    $page->checkField('allowed_blocks[Custom blocks][allowed_blocks][block_content:' . $this->blocks['Alternate Block 1'] . ']');
    // Allow Basic Block 1.
    $page->checkField('allowed_blocks[Custom blocks][allowed_blocks][block_content:' . $this->blocks['Basic Block 1'] . ']');
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    $this->drupalGet("$field_ui_prefix/display/default");
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");
    $element = $page->find('xpath', '//*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Basic Block 1');
    $assert_session->linkNotExists('Basic Block 2');
    $assert_session->linkExists('Alternate Block 1');

    // Next, add restrictions to another region and verify no contamination
    // between regions.
    // Add restriction to Second region.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="second"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    // System blocks are disallowed.
    $element = $page->find('xpath', '//*[starts-with(@id, "edit-allowed-blocks-system-restriction-whitelisted--")]');
    $element->click();
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    $this->drupalGet("$field_ui_prefix/display/default");
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");

    $element = $page->find('xpath', '//*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Powered by Drupal');
    $page->pressButton('Close');

    $element = $page->find('xpath', '//*[contains(@class, "layout__region--second")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkNotExists('Powered by Drupal');
    $page->pressButton('Close');

    // Next, allow a three-column layout and verify no contamination.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layouts-layout-threecol-section"]');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section"]/summary');
    $element->click();
    // Restrict on a per-region basis.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-restriction-behavior-per-region"]');
    $element->click();

    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="first"]', 'First');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="first"]', 'Unrestricted');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="second"]', 'Second');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="second"]', 'Unrestricted');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="third"]', 'Third');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="third"]', 'Unrestricted');

    // Manage restrictions for First region.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="first"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    $assert_session->checkboxChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxNotChecked('Allow specific Content fields blocks:');
    $assert_session->checkboxChecked('Allow all existing & new Custom blocks blocks.');
    $assert_session->checkboxNotChecked('Allow specific Custom blocks blocks:');
    $assert_session->checkboxChecked('Allow all existing & new Inline blocks blocks.');
    $assert_session->checkboxNotChecked('Allow specific Inline blocks blocks:');
    $assert_session->checkboxChecked('Allow all existing & new System blocks.');
    $assert_session->checkboxNotChecked('Allow specific System blocks:');
    $assert_session->checkboxChecked('Allow all existing & new core blocks.');
    $assert_session->checkboxNotChecked('Allow specific core blocks:');

    // Disallow Core blocks.
    $element = $page->find('xpath', '//*[starts-with(@id, "edit-allowed-blocks-core-restriction-whitelisted--")]');
    $element->click();
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="third"]', 'Third');
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-threecol-section-table"]/tbody/tr[@data-region="third"]', 'Restricted');
    $page->pressButton('Save');

    $this->drupalGet("$field_ui_prefix/display/default");
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");

    $element = $page->find('xpath', '//*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Primary admin actions');
    $page->pressButton('Close');

    $element = $page->find('xpath', '//*[contains(@class, "layout__region--second")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Primary admin actions');
    $page->pressButton('Close');

    // Add three-column layout below existing section.
    $element = $page->find('xpath', '//*[@data-layout-builder-highlight-id="section-1"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $this->clickLink('Three column');
    $assert_session->assertWaitOnAjaxRequest();
    $element = $page->find('xpath', '//*[contains(@class, "ui-dialog-off-canvas")]//*[starts-with(@id,"edit-actions-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    $this->clickLink('Manage layout');
    // Verify core blocks are unavailable to First region in
    // three-column layout.
    $element = $page->find('xpath', '//*[contains(@class, "layout--threecol-section")]/*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->linkNotExists('Primary admin actions');

    // Finally, test all_regions functionality.
    $this->drupalGet("$field_ui_prefix/display/default");

    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    // Switch Two-column layout restrictions to all regions.
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-restriction-behavior-all"]');
    $element->click();
    $assert_session->elementContains('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]', 'Unrestricted');
    $page->pressButton('Save');

    // Verify no restrictions.
    $this->clickLink('Manage layout');
    $element = $page->find('xpath', '//*[contains(@class, "layout--twocol-section")]/*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Promoted to front page');
    $page->pressButton('Close');
    $assert_session->assertWaitOnAjaxRequest();

    $element = $page->find('xpath', '//*[contains(@class, "layout--twocol-section")]/*[contains(@class, "layout__region--second")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('Promoted to front page');
    $page->pressButton('Close');
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    // Add a restriction for all_regions.
    $this->drupalGet("$field_ui_prefix/display/default");

    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-blocks-by-layout-layout-twocol-section-table"]/tbody/tr[@data-region="all_regions"]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();

    $assert_session->checkboxChecked('Allow all existing & new Content fields blocks.');
    $assert_session->checkboxNotChecked('Allow specific Content fields blocks:');

    $element = $page->find('xpath', '//*[contains(@class, "form-item-allowed-blocks-content-fields-restriction")]/input[@value="whitelisted"]');
    $element->click();
    $element = $page->find('xpath', '//*[starts-with(@id,"edit-submit--")]');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $page->pressButton('Save');

    // Verify restrictions applied to both regions.
    $this->clickLink('Manage layout');
    $element = $page->find('xpath', '//*[contains(@class, "layout--twocol-section")]/*[contains(@class, "layout__region--first")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkNotExists('Promoted to front page');
    $page->pressButton('Close');
    $assert_session->assertWaitOnAjaxRequest();

    $element = $page->find('xpath', '//*[contains(@class, "layout--twocol-section")]/*[contains(@class, "layout__region--second")]//a');
    $element->click();
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkNotExists('Promoted to front page');
    $page->pressButton('Close');
    $assert_session->assertWaitOnAjaxRequest();

    $page->pressButton('Save');
  }

  /**
   * Verify that the UI can restrict layouts in Layout Builder settings tray.
   */
  public function testLayoutRestriction() {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $field_ui_prefix = 'admin/structure/types/manage/bundle_with_section_field';
    $this->drupalGet("$field_ui_prefix/display/default");
    // Checking is_enable will show allow_custom.
    $page->checkField('layout[enabled]');
    $page->checkField('layout[allow_custom]');
    $page->pressButton('Save');
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");
    // Baseline: 'One column' & 'Two column' layouts are available.
    $assert_session->elementExists('css', '.field--name-body');
    $this->clickLink('Add section');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists('One column');
    $assert_session->linkExists('Two column');

    // Allow only 'Two column' layout.
    $this->drupalGet("$field_ui_prefix/display/default");
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts"]/summary');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layout-restriction-all"]');
    $assert_session->checkboxChecked('edit-layout-builder-restrictions-allowed-layouts-layout-restriction-all');
    $assert_session->checkboxNotChecked('edit-layout-builder-restrictions-allowed-layouts-layout-restriction-restricted');
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layout-restriction-restricted"]');
    $element->click();
    $element = $page->find('xpath', '//*[@id="edit-layout-builder-restrictions-allowed-layouts-layouts-layout-twocol-section"]');
    $element->click();
    $page->pressButton('Save');

    // Verify 'Two column' is allowed, 'One column' restricted.
    $this->drupalGet("$field_ui_prefix/display/default");
    $assert_session->linkExists('Manage layout');
    $this->clickLink('Manage layout');
    $assert_session->addressEquals("$field_ui_prefix/display/default/layout");
    $this->clickLink('Add section');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkNotExists('One column');
    $assert_session->linkExists('Two column');
  }

  /**
   * Helper function to set up block restriction-related tests.
   */
  protected function blockTestSetup() {
    // Create 2 custom block types, with 3 block instances.
    $bundle = BlockContentType::create([
      'id' => 'basic',
      'label' => 'Basic',
    ]);
    $bundle->save();
    $bundle = BlockContentType::create([
      'id' => 'alternate',
      'label' => 'Alternate',
    ]);
    $bundle->save();
    block_content_add_body_field($bundle->id());
    $blocks = [
      'Basic Block 1' => 'basic',
      'Basic Block 2' => 'basic',
      'Alternate Block 1' => 'alternate',
    ];
    foreach ($blocks as $info => $type) {
      $block = BlockContent::create([
        'info' => $info,
        'type' => $type,
        'body' => [
          [
            'value' => 'This is the block content',
            'format' => filter_default_format(),
          ],
        ],
      ]);
      $block->save();
      $blocks[$info] = $block->uuid();
    }
    $this->blocks = $blocks;
  }

}
