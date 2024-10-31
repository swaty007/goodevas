<?php

namespace Brackets\CraftablePro\Commands;

use Brackets\CraftablePro\Commands\Trait\CrudGeneratorTrait;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class CrudGeneratorCommand extends Command
{
    use CrudGeneratorTrait;

    // TODO I'm not sur about the --run-migration, shouldn't it have a value yes/no?

    /**
     * @var string
     */
    protected $signature = 'craftable-pro:generate-crud {table_name? : Table name}
                                                        {--w|wizard : Wizard mode asks for most common options interactively}
                                                        {--listing-columns= : List and select columns for index page}
                                                        {--sortable-columns= : List of columns which listing can be sorted by}
                                                        {--form-columns= : List and select columns for form page}
                                                        {--publishable-column= : Name of column which should be used for `publishable` feature}
                                                        {--translatable-columns= : List of columns which should be `translatable`}
                                                        {--media-collections= : Names of media collections}
                                                        {--image-collections= : Names of media collections which should have conversions generated as well}
                                                        {--add-relation=* : Definition of relation on model using following syntax: <relationType>(<relatedModel>-><modelAccessor>,<?foreign_key>,<?owner_key>) (ie: `belongsTo(Author->title)` or `belongsTo(Author->title,author_id,id)`)}
                                                        {--with-export : Whether listing should have export function or not}
                                                        {--run-migration : Whether to run permission migration after generating CRUD or not}
                                                        {--without-routes : Do not generate routes}
                                                        {--without-sidebar : Do not append new module into the sidebar}
                                                        {--dry-run : Display all information without actually generating anything}';
    //                                                            {--taggable : Whether taggable feature should be used or not}

    /**
     * @var string
     */
    protected $description = 'Generate CRUD';

    /**
     * @return false|int|void
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->components->info("Starting the CRUD generation");

        if ($this->argument('table_name')) {
            $this->setTableAndClassName($this->argument('table_name'));
        } else {
            $this->setTableAndClassName($this->chooseTableName());
        }

        if (! $this->tableExists()) {
            $this->components->error("Selected table '$this->tableName' does not exist!");

            return false;
        }

        $this->checkWithExistsModel();

        $this
            ->getAndSetListingColumns()
            ->getAndSetSortableColumns()
            ->getAndSetFormColumns()
            ->getAndSetTranslatableColumns()
            ->getAndSetPublishableColumn()
            //            ->getAndSetTaggable()
            ->getAndSetMediaCollections()
            ->getAndSetImageCollections()
            ->getAndSetRelations()
            ->getAndSetWithExport();

        if ($this->option("dry-run")) {
            $this->buildDryRun();

            return 0;
        }

        $this->buildModel()
            ->buildRequests("index")
            ->buildRequests("create", "create")
            ->buildRequests("store", "create")
            ->buildRequests("edit", "edit")
            ->buildRequests("update", "edit")
            ->buildRequests("destroy")
            ->buildRequests("bulkDestroy")
            ->buildController()
            ->buildExport()
            ->buildViews();

        if (! $this->option('without-routes')) {
            $this->buildRoutes();
        }

        if (! $this->option('without-sidebar')) {
            $this->appendLinkInSidebar();
        }

        $this->buildPermissionsMigration();

        if ($this->option('run-migration') || $this->components->ask("Migration introducing new permissions for model '$this->className' was created. Do you want to run `artisan migrate` now?", "yes") === "yes") {
            $this->call('migrate');
        }

        $this->call("craftable-pro:scan-translations");
        $this->call("craftable-pro:publish-translations");

        $this->components->info("CRUD for model '$this->className' was generated successfully!");
    }
}
