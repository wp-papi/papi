## Contributing to Papi

Everyone is welcome to contribute with patches, bug-fixes and new features.

## Ideas

If you have a idea the easiest way is to create a [issue](https://github.com/wp-papi/papi/issues).

## Bugs

Before you submit a bug issue you should be able to run the tests that the project has. Then you know if the tests works or not.

When you submit a bug issue you should write which browser you are using since Papi contains front-end code and not only back-end code.

If a test fails you should tell which one so it's easier to know what the bug is about.

Try to be as detailed as possible in your bug issue so we can help you better with the issue.

**Please** write how to reproduce the bug.

## Which branch?

The `master` branch is unsafe but it should be used when you adding new features. When it's a bug fix that effects the current version you should do your pull request against the last stable branch. The stable branch will have the format `MAJOR.X.X`.

## Pull requests

Good pull requests—patches, improvements, new features—are are allways welcome. They should remain focused in scope and avoid containing unrelated commits.

**Please ask first** embarking on any significant pull request (e.g. implementing features, refactoring code, porting to a different language), otherwise you risk spending a lot of time working on something that the project's developers might not want to merge into the project.

**Please follow** the projects code style. The projects PHP code should be following the [WordPress code standard](https://make.wordpress.org/core/handbook/coding-standards/php/) and always use brackets.

* Fork [wp-papi/papi](https://github.com/wp-papi/papi) on Github and add the upstream remote.

```
git clone https://github.com/<your-username>/papi.git
cd papi
git remote add upstream https://github.com/wp-papi/papi.git
```

This is useful if you cloned your repo a while ago and want's to updated it.

```
git checkout master
git pull upstream master
```

* Create a new branch:

```
git checkout -b <topic-branch-name>
```

* Make sure to update, or add to the tests when appropriate.
* Commit your changes to your fork.
* Locally merge (or rebase) the upstream development branch into your topic branch:

```
git pull [--rebase] upstream master
```

* Push to your branch:

```
git push origin <topic-branch-name>
```

* [Open a Pull Request](https://help.github.com/articles/using-pull-requests/) with a clear title and description against the `master` branch or against the stable branch if it's a bug fix that effects the current version.

**Note:**
If you are making several changes at once please divide them into multiple pull requests.

## License

By contributing your code, you agree to license your contribution under the [MIT license](https://github.com/wp-papi/papi/blob/master/LICENSE).
