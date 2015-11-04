Feature: Manage types
  Scenario: List all types

    When I run `wp papi type list --format=csv`
    Then STDOUT should be CSV containing:
      | name | id | post_type | template | number_of_pages | type |
