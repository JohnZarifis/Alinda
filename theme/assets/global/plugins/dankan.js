	    function submit_new_dates(){
		var form = document.createElement("form");
		form.style.display = "none";
		form.method = "POST";
		form.action = "print.php"; //<<<<<<<<<<<<<<<<<<<<perhaps send it to the same page.
		form.acceptCharset = "utf-8";
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "apo_value");
		hiddenField.setAttribute("value", document.getElementById("apo_value").value);
		form.appendChild(hiddenField);

		var hiddenField2 = document.createElement("input");
		hiddenField2.setAttribute("type", "hidden");
		hiddenField2.setAttribute("name", "mexri_value");
		hiddenField2.setAttribute("value", document.getElementById("mexri_value").value);
		form.appendChild(hiddenField2);

		
		document.body.appendChild( form );
		form.submit();
		document.body.removeChild( form );
}
