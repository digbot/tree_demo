if (typeof console == "undefined" || typeof console.log == "undefined") {
    var console = { log: function() {} }; 
}

$(document).ready(function() {
    
var tdBaseObj = {
    debug: false,
    serverIndex: '/ajax',
    refresh: function() {
        var _self = this;
        $.ajax({
            url: this.serverIndex,
            cache: false,
            type: "GET",
            dataType: "html",
            data: ({}),
            success: function(htmlData) {
                _self.render(htmlData);
            },
            error: function() { 
              console.log('Someting went wrong....'); 
            } 
        });	
    }
}

var tdTreeNode = {
    init: function() {
        this.serverIndex = 'node'; 
    },
    bindSubmitForms: function() {
        $('#edit_node_form').on('submit', this.submitNode);  
        $('#add_node_form').on('submit', this.submitNode);
    },
    submitNode: function (event) {
            event.preventDefault();
            $this = $(this);
            $.post($this.attr('action'), $this.serialize(), function (data, textStatus, jqXHR) {
                if (200 == jqXHR.status) {
                        tdTreeIndex.refresh(); 
                } else {
                        console.log('Someting went wrong....'); 
                }
            }).fail(function(jqXHR, textStatus, errorThrown) { 
                 if (422 == jqXHR.status) {
                      var formHTML    =  $("<div><div>").html(jqXHR.responseText).find('form').html();
                      var currontForm = $this.find('form');
                      $this.html(formHTML);
                       
                 }
            }); 
    },
    render: function(htmlData) {
        var _self = this;
        $('#tree_node_wrap').html(htmlData);
        this.bindSubmitForms();
        
        $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
         });  

        $('a.breadcrumb_node_link').bind('click', function() {
            var href = $(this).attr('href');
            var thenum = href.replace( /^\D+/g, '');
            tdTreeNode.serverIndex = 'node/' + thenum;  
            tdTreeNode.refresh();
            return false;
        });

    }
};

var tdTreeIndex = {
    init: function() {
        this.refresh();
    },
    render: function(htmlData) {
    var tree = $('#js_tree_wrap')
            .html(htmlData)
            .jstree();

        tree.jstree("select_node", '#node1', true);
        tree.bind("loaded.jstree", function (event, data) {
                tree.jstree("open_all");
                data.inst.select_node("#tree_node_1", true);
        });

        tree.bind("select_node.jstree", function (event, data) {
             event.preventDefault();
            // `data.rslt.obj` is the jquery extended node that was clicked
            var href = data.rslt.obj.find('a').attr('href');
            var thenum = href.replace( /^\D+/g, '');
            tdTreeNode.serverIndex = 'node/' + thenum;  
            tdTreeNode.refresh();
            return false;
        });
    }
};

$.extend(tdTreeIndex, tdBaseObj);
tdTreeIndex.init();
$.extend(tdTreeNode, tdBaseObj);
tdTreeNode.init();


});
