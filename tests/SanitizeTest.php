<?php

class SanitizeTest extends PHPUnit\Framework\TestCase {

  private function sanitize($slug, $opts=[]) {
    if(!isset($opts['baseURL']))
      $opts['baseURL'] = 'http://sanitize.example';

    $html = file_get_contents(__DIR__.'/data/'.$slug.'.html');
    return p3k\HTML::sanitize($html, $opts);
  }

  public function testAllowsValidTags() {
    $html = $this->sanitize('entry-with-valid-tags');

    $this->assertStringContainsString('This content has only valid tags.', $html);
    $this->assertStringContainsString('<a href="http://sanitize.example/example">links</a>,', $html, '<a> missing');
    $this->assertStringContainsString('<abbr>abbreviations</abbr>,', $html, '<abbr> missing');
    $this->assertStringContainsString('<b>bold</b>,', $html, '<b> missing');
    $this->assertStringContainsString('<code>inline code</code>,', $html, '<code> missing');
    $this->assertStringContainsString('<del>delete</del>,', $html, '<del> missing');
    $this->assertStringContainsString('<em>emphasis</em>,', $html, '<em> missing');
    $this->assertStringContainsString('<i>italics</i>,', $html, '<i> missing');
    $this->assertStringContainsString('<img src="http://sanitize.example/example.jpg" alt="images are allowed" />', $html, '<img> missing');
    $this->assertStringContainsString('<q>inline quote</q>,', $html, '<q> missing');
    $this->assertStringContainsString('<strike>strikethrough</strike>,', $html, '<strike> missing');
    $this->assertStringContainsString('<strong>strong text</strong>,', $html, '<strong> missing');
    $this->assertStringContainsString('<time datetime="2016-01-01">time elements</time>', $html, '<time> missing');
    $this->assertStringContainsString('<blockquote>Blockquote tags are okay</blockquote>', $html);
    $this->assertStringContainsString('<pre>preformatted text is okay too', $html, '<pre> missing');
    $this->assertStringContainsString('for code examples and such</pre>', $html, '<pre> missing');
    $this->assertStringContainsString('<p>Paragraph tags are allowed</p>', $html, '<p> missing');
    $this->assertStringContainsString('<h1>One</h1>', $html, '<h1> missing');
    $this->assertStringContainsString('<h2>Two</h2>', $html, '<h2> missing');
    $this->assertStringContainsString('<h3>Three</h3>', $html, '<h3> missing');
    $this->assertStringContainsString('<h4>Four</h4>', $html, '<h4> missing');
    $this->assertStringContainsString('<h5>Five</h5>', $html, '<h5> missing');
    $this->assertStringContainsString('<h6>Six</h6>', $html, '<h6> missing');
    $this->assertStringContainsString('<ul>', $html, '<ul> missing');
    $this->assertStringContainsString('<li>One</li>', $html, '<li> missing');
    $this->assertStringContainsString('<p>We should allow<br />break<br />tags too</p>', $html, '<br> missing');
  }

  public function testDisallowsImages() {
    $html = $this->sanitize('entry-with-valid-tags', [
      'allowImg' => false
    ]);

    $this->assertStringNotContainsString('<img src="http://sanitize.example/example.jpg" alt="images are allowed" />', $html);
  }

  public function testRemovesUnsafeTags() {
    $html = $this->sanitize('entry-with-unsafe-tags');

    $this->assertStringContainsString('<b>valid ones</b>', $html);
    $this->assertStringNotContainsString('<script>', $html);
    $this->assertStringNotContainsString('<style>', $html);
    $this->assertStringNotContainsString('visiblity', $html); // from the CSS
    $this->assertStringNotContainsString('alert', $html); // from the JS
  }

  public function testAllowsMF2Classes() {
    $html = $this->sanitize('entry-with-mf2-classes');

    $this->assertStringContainsString('<h2 class="p-name">Hello World</h2>', $html);
    $this->assertStringContainsString('<h3>Utility Class</h3>', $html);
  }

  public function testRemovesMF2Classes() {
    $html = $this->sanitize('entry-with-mf2-classes', [
      'allowMf2' => false
    ]);

    $this->assertStringContainsString('<h2>Hello World</h2>', $html);
    $this->assertStringContainsString('<h3>Utility Class</h3>', $html);
  }

  public function testEscapingHTMLTagsInHTML() {
    $html = $this->sanitize('html-escaping-in-html');

    $this->assertEquals('<p class="e-content">This content has some <i>HTML escaped</i> entities such as &amp; ampersand, " quote, escaped &lt;code&gt; HTML tags, an ümlaut, an @at sign.</p>', $html);
  }

  public function testSanitizeAttributes() {
    $html = $this->sanitize('attribute-test');

    $this->assertStringContainsString('<p>remove class</p>', $html);
    $this->assertStringContainsString('<p>remove style</p>', $html);
    $this->assertStringContainsString('<a>remove data</a>', $html);
    $this->assertStringContainsString('<h2 class="p-name">Hello</h2>', $html);
    $this->assertStringContainsString('<a href="http://sanitize.example/">keeps href</a>', $html);
    $this->assertStringContainsString('<img src="http://sanitize.example/photo.jpg" width="10" alt="photo.jpg" />', $html);
    $this->assertStringContainsString('<time datetime="2019-11">November 2019</time>', $html);
  }

  public function testRemovesTablesByDefault() {
    $html = $this->sanitize('tables');

    $this->assertStringNotContainsString('<table>', $html);
    $this->assertStringNotContainsString('<tr>', $html);
    $this->assertStringNotContainsString('<td>', $html);
  }

  public function testAllowsTables() {
    $html = $this->sanitize('tables', ['allowTables' => true]);

    $this->assertStringContainsString('<table>', $html);
    $this->assertStringContainsString('<thead>', $html);
    $this->assertStringContainsString('<tbody>', $html);
    $this->assertStringContainsString('<tfoot>', $html);
    $this->assertStringContainsString('<tr>', $html);
    $this->assertStringContainsString('<th>', $html);
    $this->assertStringContainsString('<td>', $html);
  }

}
