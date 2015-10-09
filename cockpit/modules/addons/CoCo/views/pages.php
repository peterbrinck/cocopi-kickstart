<style media="screen">
    canvas {
        max-width: 100%;
        height: auto;
    }
</style>
<div class="uk-form" riot-view>

    <ul  class="uk-breadcrumb">
        @render('coco:views/partials/subnav.php')
        <li each="{p in parents}" data-uk-dropdown>
            <a href="@route('/coco/page'){ p.relpath }"><i class="uk-icon-home" if="{p.isRoot}"></i> { p.meta.title.substring(0, 15) }</a>
            <div class="uk-dropdown">
                <ul class="uk-nav uk-nav-dropdown">
                    <li class="uk-nav-header">@lang('Browse')</li>
                    <li><a href="@route('/coco/pages'){p.relpath}">@lang('Sub Pages')</a></li>
                    <li><a href="@route('/coco/files'){p.relpath}">@lang('Files')</a></li>
                </ul>
            </div>
        </li>
        <li data-uk-dropdown>
            <a href="@route('/coco/page'.$page->relpath())"><i class="uk-icon-home" if="{page.isRoot}"></i> { page.meta.title.substring(0, 15) }</a>
            <div class="uk-dropdown">
                <ul class="uk-nav uk-nav-dropdown">
                    <li class="uk-nav-header">@lang('Browse')</li>
                    <li><a href="@route('/coco/files'){page.relpath}">@lang('Files')</a></li>
                </ul>
            </div>
        </li>
        <li><span class="uk-text-primary">@lang('Pages')</span></li>
    </ul>

    <div class="uk-margin" if="{children.length}">
        <a class="uk-button uk-button-primary" onclick="{ createPage }">@lang('Create Page')</a>

        <div class="uk-form-icon uk-form uk-text-muted uk-float-right">

            <i class="uk-icon-filter"></i>
            <input class="uk-form-large uk-form-blank" type="text" name="txtfilter" placeholder="@lang('Filter pages...')" onkeyup="{ update }">

        </div>
    </div>

    <div class="uk-grid uk-grid-match uk-grid-width-medium-1-3 uk-grid-width-large-1-4" if="{children.length}">

        <div class="uk-grid-margin" each="{child,idx in children}" show="{ parent.infilter(child) }">
            <div class="uk-panel uk-panel-box uk-panel-card">
                <div class="uk-flex">
                    <span class="uk-margin-small-right" data-uk-dropdown>
                        <i class="uk-icon-file-text-o uk-text-{ child.visible ? 'success':'danger' }"></i>
                        <div class="uk-dropdown">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li class="uk-nav-header">@lang('Browse')</li>
                                <li><a href="@route('/coco/pages'){child.relpath}">@lang('Sub Pages')</a></li>
                                <li><a href="@route('/coco/files'){child.relpath}">@lang('Files')</a></li>
                                <li class="uk-nav-divider"></li>
                                <li><a onclick="{ parent.remove }" data-path="{ child.path }">@lang('Delete')</a></li>
                            </ul>
                        </div>
                    </span>
                    <a class="uk-flex-item-1 uk-text-truncate" href="@route('/coco/page'){ child.relpath }">{ child.meta.title }</a>
                </div>
                <div class="uk-position-relative">
                    <canvas width="600" height="400"></canvas>
                    <a class="uk-position-cover" href="@route('/coco/page'){ child.relpath }"></a>
                </div>
                <div class="uk-margin-small-top uk-text-small uk-text-muted">
                    { child.type }
                </div>
            </div>
        </div>
    </div>

    <div class="uk-margin-large-top uk-viewport-height-1-3 uk-container-center uk-text-center uk-flex uk-flex-middle uk-flex-center" if="{!children.length}">

        <div class="">

            <h3>{ page.meta.title }</h3>

            <p>
                { App.i18n.get('This page has no sub-pages.') }
            </p>
            <p>
                <a class="uk-button uk-button-large uk-button-primary" onclick="{ createPage }">@lang('Create one')</a>
            </p>
        </div>
    </div>


    <script type="view/script">

        var $this = this;

        this.mixin(RiotBindMixin);

        this.page     = {{ json_encode($page->toArray()) }};
        this.children = {{ json_encode($page->children()->toArray()) }};
        this.parents  = {{ json_encode(array_reverse($page->parents()->toArray())) }};

        createPage(e) {

            coco.createPage(this.page.isRoot ? '/':this.page.contentdir);
        }

        remove(e) {

            var path = e.item.child.path;

            App.ui.confirm("Are you sure?", function() {

                App.callmodule('coco', 'deletePage', [path]).then(function(data) {

                    $this.children.splice(e.item.idx, 1);
                    $this.update();
                });
            });
        }

        infilter(page, value, name) {

            if (!this.txtfilter.value) {
                return true;
            }

            value = this.txtfilter.value.toLowerCase();
            name  = page.meta.title;

            return name.indexOf(value) !== -1;
        }

    </script>

</div>