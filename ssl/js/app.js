// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
// $(document).foundation();
var rechercher=false;
var derniereRecherche = "";
var monApp = $(document).ready( function configuration() {
	$('.subnav-icon.panier').before('<button type="button" class="subnav-icon search"><span class="visuallyhidden">Chercher un livre</span></button>')
	$('#motsCles').after('<div id="autoComplete"></div>');
	$('.confirmation .sommaire').append('<button id="print">Imprimer la commande</button>');
	rechercher = true;
	$("#motsCles").keyup(demanderAjax);
	$("#motsCles").change(demanderAjax);
	$("#motsCles").blur(quitterChampDeSaisie);
	$("#critere").change(changerCritere);

	$('#print').click(function imprimer() {
		window.print();
	})

	$(document).ajaxStart(function montreProgres(){
		$('.indicateurDeProgres').show();
	});
	$(document).ajaxStop(function cacheProgres(){
		$('.indicateurDeProgres').hide();
	});

	$('.mobile-button').prepend("<button type='button' class='menu-mobile'><span class='visuallyhidden'>Menu mobile</span></button>");
	$('.menu-mobile').on('click', mobileMenuManagement);
	$('.search').on('click', searchbarManagement);
	$(".recherche").hide();
	$(".achat").hide();
	$("#addtocart").click(ajouterAuPanier);

	$('a[href^="#"]').click(function ancreFluide(){  
	    var the_id = $(this).attr("href");  
	  
	    $('html, body').animate({  
	        scrollTop:$(the_id).offset().top  
	    }, 'slow');  
	    return false;  
	});
});

function demanderAjax(event){
	if($("#motsCles").val().length>0 && rechercher){
		if($("#motsCles").val()!=derniereRecherche){
			var donnees = "motsCles=" + $('#motsCles').val() + "&categorie="+$("#categorie").val();
			$.ajax({
				type:"GET",
				url:"inc/scripts/autocompletion.php",
				data: donnees,
				success: function(html){
					$('#autoComplete').html(html);
					$('#autoComplete').show();
					$('#autoComplete li').click(cliquerHover);
				}
			});
			derniereRecherche = $("#motsCles").val();
		}
	}else{
		$("#autoComplete").hide();
	}
}

function cliquerHover(event){
	$("#motsCles").val($(this).text());
	$("#autoComplete").hide();
}

function quitterChampDeSaisie(event){
	setTimeout("$('#autoComplete').hide();", 400);
}

function changerCritere(event){
	var arrPermis = Array("auteur", "titre");
	$("#motsCles").val("");
	if(arrPermis.indexOf($(this).val()) == -1){
		$("#autoComplete").hide();
		rechercher = false;
	}else{
		rechercher = true;
	}
}


function mobileMenuManagement(event){
	if($('.sidebar').css('display') == 'block'){
		$('.sidebar').css('display', 'none');
	}else{
		$('.sidebar').css('display', 'block');
	}
}

function searchbarManagement(event){
	if ($(".recherche").is( ":hidden")) {
	    $(".recherche").slideDown();
	  } else {
	    $(".recherche").slideUp();
	}
}

function ajouterAuPanier(event){
	event.preventDefault();
    $('html, body').animate({
        scrollTop: $('body').offset().top
    }, 200);
    var donnees = "ajouter="+$("#addtocart").val();

	$.ajax({
		type:"GET",
		url:"inc/scripts/addToCart.ajax.php",
		data: donnees,
		success: function(html){
			$(".achat").html(html);
		}
	});
	$(".achat").slideDown();
}
