$(document).ready(function() {
       var tree = $("#js_tree_wrap").jstree();
       tree.bind("loaded.jstree", function (event, data) {
           tree.jstree("open_all");
           
           ///  data.inst.select_node("#tree_node_1", true);
       });
       
  
        tree.bind("select_node.jstree", function (event, data) {
            event.preventDefault();
            // `data.rslt.obj` is the jquery extended node that was clicked
            var href = data.rslt.obj.find('a').attr('href');
            window.location = href;
             
          /// return true;
            
        });


        $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        })
        
      
         
         
//        $("#js_tree_wrap").bind("open_node.jstree", function (e, data) {
//            // data.inst is the instance which triggered this event
//            //    data.inst.select_node("#tree_node_1", true);
//            //    a
//         ///  
//        });



});
