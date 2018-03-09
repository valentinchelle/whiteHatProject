web3.eth.getBlockNumber(function(error, result) {
    if (error) {
      console.log(error);
    }else{
      console.log("Finding the hash..");
      web3.eth.getBlock(result - 1, true, function(error, result){
        console.log(result);
      });
    }
});
