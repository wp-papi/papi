Feature: WP-CLI
	In order to remove old revisions
	as a site owner
	I should be able to use WP-CLI

Background:
	Given a WP install
	When I run `wp plugin activate papi`
    Then STDOUT should be:
    	"""
			Success: Plugin 'papi' activated.
    	"""
