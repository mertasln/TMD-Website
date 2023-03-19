import { component } from 'picoapp';
import options from '@/lib/options.js';

export default component(({ node }) => {
  // var accountID = 5064;
  const opts = options(node);

  var viewerInstance = null;

  var getFeatures = function() {
    let set_id = getVariantId();
    var existCondition = setInterval(function() {
      set_id = getVariantId();
      if (set_id !="" && set_id !== undefined) {
        console.log('CYLINDO SET ID ' + set_id);
        clearInterval(existCondition);
        var variant = cylindoProductVariants.find(function(el) {
          return el.id == set_id;
        });
        console.log( 'CYLINDO FEATURES' + variant.features);
        return variant.features || [];
      }
    }, 100); // check every 100ms
  }
  // prepare the first set of features.
  var features = getFeatures();
  
  var cylindo_options = {
    'accountID': 5064,
    'productCode': productData.productCode,
    'features': features,
    'containerID': 'cylindo-viewer',
    'thumbCount': 5,
    'thumbs': true,
    'zoom': true,
    'progressBar': false,
    'backgroundColor': 'f7f7f7',
    'alternateContent': alternateContent,
    'fullscreen': false,
    'ARDesktop': true,
  }

  if (cylindo) {
    cylindo.on('ready', function(){
      console.log('CYLINDO READY');
      viewerInstance = cylindo.viewer.create(cylindo_options);

      var size_inputs=document.querySelectorAll("input[name=Size]"),x=size_inputs.length;
      while(x--) {
              size_inputs[x].addEventListener("change",function(){
                viewerInstance.setProduct(productCode+'-'+this.value.replace('"','').replace('”','').replace('King','K').replace('Queen','Q').replace('Small','S').replace('Large','L'));
                // console.log("Checked: "+this.checked);
                // console.log("Name: "+this.name);
                // console.log("Value: "+this.value);
                // console.log("Parent: "+this.parent);
          },0);
      }
      var size_inputs=document.querySelectorAll("input[name=Configuration]"),x=size_inputs.length;
      while(x--) {
              size_inputs[x].addEventListener("change",function(){
                viewerInstance.setProduct(productCode+'-'+this.value.replace('Left Chaise','L').replace('Right Chaise','R'));
                // console.log("Checked: "+this.checked);
                // console.log("Name: "+this.name);
                // console.log("Value: "+this.value);
                // console.log("Parent: "+this.parent);
          },0);
      }

      opts.onUpdate(state => {
        let set_id = getVariantId();
        var existCondition = setInterval(function() {
          set_id = getVariantId();
          if (set_id !="" && set_id !== undefined) {
            console.log('CYLINDO SET ID ' + set_id);
            clearInterval(existCondition);
            var variant = cylindoProductVariants.find(function(el) {
              return el.id == set_id;
            });
            console.log( 'CYLINDO FEATURES' + variant.features);
            viewerInstance.setFeatures(variant.features || []);
          }
        }, 100); // check every 100ms
        // features = getFeatures();
        // console.log('CYLINDO FEATURES RELOADED: ' + features);
        // viewerInstance.setFeatures(features);
      });
    });
  }
  function getVariantId(){
      var theID = document.getElementById('selectid').value;
      console.log('CYLINDO theID A ' + theID);
      if(theID !="" && theID !== undefined){
          return theID;
      }
      else{
          console.log('CYLINDO LOOPING');
          console.log('CYLINDO theID B ' + theID);
          setTimeout(getVariantId, 250);
      }
  }
});