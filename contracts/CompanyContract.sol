pragma solidity ^0.4.17;

contract CompanyContract{
    address public contractCreator;
    uint public bounty;
    bytes32 public hashCode;
    //event that allows us to get the result of the attempt on the web app.
    event attemptCast(address add, uint message);

    function CompanyContract(string pass) payable public {
        bounty = msg.value;
        contractCreator = msg.sender;
        hashCode = keccak256(pass);
    }

    function passAttempt(string pass) public {
        if(keccak256(pass) == hashCode){
            // The pass has been found.
            // The sender gets the bounty
            attemptCast(msg.sender,1111);
            require(msg.sender.send(bounty));
            
        }
        else{
            attemptCast(msg.sender,1010);
        }
    }

}