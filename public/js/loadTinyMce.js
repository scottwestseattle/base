
if (isMobile.any())
{
	tinymce.init({
		selector:'#text',
		plugins: 'table code lists',
		
		mobile: {
			theme: 'silver'
		  }	
	});
	
	console.log('tinymce loaded - mobile');	
}
else
{
	tinymce.init({
		selector:'#text',
		plugins: 'table code lists',
		toolbar: 'code formatselect | bold italic forecolor backcolor permanentpen formatpainter | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
	});	
	
	console.log('tinymce loaded - desktop');
}


