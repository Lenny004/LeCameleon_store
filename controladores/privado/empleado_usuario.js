//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function() {
    //Var se crea para variables globales
    //let es variables locales

    //Instanciar el menú
    M.Sidenav.init(document.querySelectorAll('.sidenav'));

    //Instanciar Dropdown Menú
    var elems = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elems, {coverTrigger:false, hover: true});

    //Instanciar Select
    M.FormSelect.init(document.querySelectorAll('select'));
    
    //Instanciar Slider del main;
    let options = {indicators: false, height: 500};
    M.Slider.init(document.querySelectorAll('.slider'), options);

    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    //Instaciar el modal o pow up
    M.Modal.init(document.querySelectorAll('.modal'));

    M.Modal.init(document.querySelectorAll('.moda2'));
    
    M.Modal.init(document.querySelectorAll('.moda3'));
  });

          