Feature: Manage types

  Background:
	 Given a WP install
	  When I run `wp plugin activate papi`
    Then STDOUT should be:
    	"""
			Success: Plugin 'papi' activated.
    	"""

  Scenario: List all types

    When I run `wp papi type list --format=csv`
    Then STDOUT should be CSV containing:
      | name | id | post_type | template | number_of_pages | type |
