# Changelog

All notable changes to `craftable-pro` will be documented in this file.

## v1.1.5 - 2023-03-16

### What's Changed

- fix: significantly reduce chunk size after build by @vsotreshko in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/237
- fix: position of locale input on register page by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/253
- fix: unify return types in generated controller by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/245
- fix: remove show route from generated routes, since it just throws error by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/246
- fix: redirect to previous listing route after model update by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/251
- fix: reset listing page to 1 on search and filter by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/252
- fix: improve type definitions in some Vue components by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/249
- feat: improve customization of inputs in dropzone by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/248
- fix: some overall tweaks and fixes by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/258

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.1.4...v1.1.5

## v1.1.4 - 2023-03-01

### What's Changed

- build(deps): bump json5 from 1.0.1 to 1.0.2 by @dependabot in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/242
- Fix not replacing in Index.vue by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/244

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.1.3...v1.1.4

## v1.1.3 - 2023-02-24

### What's Changed

- Fix: Logged in user should not be able to sue impersonate login on himself by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/233
- Fix: Added 'detach' in 'destroy' function for 'belongsToMany' relation by @vsotreshko in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/236
- Fix: Generator incorrectly detects existing model by @palypster in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/239
- Fix: Add missing destroy request to generator by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/241
- Fix: Make model name plural on index page in generated files by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/240

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.1.2...v1.1.3

## v1.1.2 - 2023-02-22

### What's Changed

- Fix: bug on manage permissions screen preventing editation by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/224
- Fix: add page title to meta tags as well by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/226
- Fix: generator should provide options to choose table name by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/227
- Fix: make translations scanner case sensitive in default Laravel installation with MySQL by @palypster in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/230
- Fix: generator should detect existing eloquent model and if present it should ask if it should be overwritten by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/231
- Fix: resend invitation error by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/232
- Fix: Add translations edit button check permissions by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/234
- Fix: add baseUrl as computed for filters and listing by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/228
- Fix: change generated export name by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/225
- Fix: export created and updated datetimes, fix translations, fix sidebar by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/223

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.1.1...v1.1.2

## v1.1.1 - 2023-02-15

### What's Changed

- Fix: remove forgotten console.log's by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/222
- Fix: InertiaJS types by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/221
- Fix: add custom guest middleware to prevent redirects to '/home' by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/220

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.1...v1.1.1

## v1.1 - 2023-02-14

### What's Changed

- bugfix: Fixed kebabcase to snakecase for generated relation name by @vsotreshko in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/217
- feat: Laravel 10 support by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/216
- fix: add custom 'verified' middleware to prevent bug by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/218
- fix: requests generating with wrong namespaces by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/219

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.0.4...v1.1

## v1.0.4 - 2023-02-13

### What's Changed

- Fix: generator generating wrong and incomplete JS types by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/215

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.0.3...v1.0.4

## v1.0.3 - 2023-02-10

### What's Changed

- Fix: custom Auth middleware and edit tests to use named routes by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/214
- Fix: prefix to routes with `craftable-pro` by @stringsam in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/213

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.0.2...v1.0.3

## v1.0.2 - 2023-02-10

### What's Changed

- Fix: image collection option bug by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/209
- Fix: let menu dropdown float outside of main content by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/212
- Fix: generator related fixes by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/208

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.0.1...v1.0.2

## v1.0.1 - 2023-02-09

### What's Changed

- Fix not translated export headings by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/207
- fix: controller name causing error in 'route:list' command by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/210

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v1.0.0...v1.0.1

## v1.0.0 - 2023-02-07

### 🎉 Initial release 🎉

## v0.0.9 - 2023-02-07

### What's Changed

- Fix/last minute fixes by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/204
- Fix/last minute fixes by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/206
- Install command UX tweaks by @palypster in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/205

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v0.0.8...v0.0.9

## v0.0.7 - 2023-02-07

### What's Changed

- fix: tag for permissions vendor publish changed by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/203

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v0.0.6...v0.0.7

## v0.0.6 - 2023-02-07

### What's Changed

- feat: add more wysiwyg reserved column names by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/184
- feat: add support for sortable columns by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/183
- fix: update index columns in generator to use consistent dateformat by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/186
- fix: stop generator when table does not exist by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/187
- fix: missing laravel-vite-plugin by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/188
- Fix save settings default route by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/189
- Fix naming by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/192
- Fix generate dropzone with media collections by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/191
- Feature/pro 240 generator publishable feature by @vsotreshko in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/185
- Remove taggable option from generator cmd by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/194
- Generate output command with dry run by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/193
- Remove if statement and change collection to array by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/196
- Pro 341 generated publishable does not work correctly when column is not null by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/195
- fix: change namespace for controllers by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/190
- fix: locale select by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/197
- feat: make publishable css file prettier by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/198
- fix: visual tweaks by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/200
- Pro 325 after generator is done verify if all options are being respected by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/201
- Pro 342 generating publishable in non wizard mode does not work by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/202
- add check translatable columns is in json or jsonb column type by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/199

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v0.0.5...v0.0.6

## v0.0.5 - 2023-02-03

### What's Changed

- Feat: add support for adding media collections to model by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/176

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v0.0.4...v0.0.5

## v0.0.4 - 2023-02-03

### What's Changed

- Feat: Generator tweaks - using new laravel way by @palypster in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/168
- Feat: settings redesign by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/170
- Feat: Introducing options to not generate sidebar or routes by @palypster in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/171
- Feat: support for export in generator by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/172
- Feat: SortNullsLast by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/175
- Fix: email validation rule, small refactor by @rastik1584 in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/177
- Feat: add locale flags to translatable fields labels by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/181
- Feat: support for translatable model by @vsotreshko in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/182
- Feat: support for relations in generator by @timoransky in https://github.com/BRACKETS-by-TRIAD/craftable-pro/pull/174

**Full Changelog**: https://github.com/BRACKETS-by-TRIAD/craftable-pro/compare/v0.0.3...v0.0.4

## v0.0.2 - 2023-01-24

### What's Changed

- fixed bug in generator generating wrong permission names

## 1.0.0 - 202X-XX-XX

- initial release
