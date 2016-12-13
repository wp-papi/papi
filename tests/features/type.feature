Feature: Manage types

  Background:
	 Given a WP install
	  When I run `wp plugin activate papi`
    Then STDOUT should contain:
    	"""
			Plugin 'papi' activated.
    	"""

  Scenario: List all types
    When I run `wp papi type list --format=csv`
    Then STDOUT should be CSV containing:
      | name | id | meta type | meta type value | template | db count | type |
      | Attachment | others/attachment-type | post | attachment | n/a | 0 | attachment |
      | Header | options/header-option-type | option | n/a | n/a | n/a | option |
      | Properties page type | properties-page-type | post | page | pages/properties-page.php | 0 | page |
      | Properties taxonomy type | properties-taxonomy-type | taxonomy | post_tag | pages/properties-taxonomy.php | 0 | taxonomy |
