(function(w, d, base) {
    if (!base.composer) return;
    base.composer.button('clone plugin-page-splitter', {
        title: base.languages.MTE.plugin_page_splitter.title.split,
        position: 12,
        click: function(e, editor) {
            var grip = editor.grip,
                s = grip.selection();
            if (!s.before.length || !s.after.length) {
                return grip.select(), false;
            }
            grip.tidy('\n\n', function() {
                grip.insert('<!-- next -->', function() {
                    s = grip.selection();
                    if (!s.after.length) grip.area.value += '\n\n';
                    grip.select(s.end + 2, true);
                });
            }, '\n\n', true);
        }
    });
})(window, document, DASHBOARD);