var express = require('express');
var app = express();
var path = require('path');
var formidable = require('formidable');
var fs = require('fs');

app.use(express.static(path.join(__dirname, 'public')));

app.get('/', function(req, res){
  res.sendFile(path.join(__dirname, 'views/index.html'));
});

app.post('/upload', function(req, res){
    var form = new formidable.IncomingForm();
    form.multiples = true;
    form.uploadDir = path.join(__dirname, '/uploads');
    form.on('file', function(field, file) {
	var sys = require('sys')
	var exec = require('child_process').exec;
	var child;

	console.log(form.uploadDir);
	console.log(file.name);
	fs.rename(file.path, path.join(form.uploadDir, file.name));
	child = exec("php ./bin/parse.php " + form.uploadDir + '/' + file.name, function (error, stdout, stderr) {
	    if (error !== null) {
		console.log('exec error: ' + error);
	    }
	});

	
    });

  form.on('error', function(err) {
    console.log('An error has occured: \n' + err);
  });

  form.on('end', function() {
    res.end('success');
  });

  form.parse(req);
});

var server = app.listen(3000, function(){
  console.log('Server listening on port 3000');
});
