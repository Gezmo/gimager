# gimager
Get Images Remotely
This API functions as an image keeper.
It stores images supplied in bulk through text files containing the online URL's to the images. A local copy of the image file is made if the given URL is valid and available. The text file with images must have a header line and a separate line for every image. All lines must have the same number of fields. Fields are: image_name, image_url, image_description, comment. Only the first two fields are mandatory. The text file must be supplied as a POST value with the name csv (string).
It supplies a list of metadata of all available images on request without an id.
It returns a single image URL when requested with an id using the stored URL if still availble or the URL of the local copy if the original file is no longer available.
