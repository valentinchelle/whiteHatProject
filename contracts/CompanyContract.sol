pragma solidity ^0.4.17;

contract CompanyContract{
    address public contractCreator;
    uint public bounty;
    bytes32 public hashCode;

    function CompanyContract(string pass) payable public {
        bounty = msg.value;
        contractCreator = msg.sender;
        hashCode = keccak256(pass);
    }

    function passAttempt(string pass) public {
        if(keccak256(pass) == hashCode){
            // The pass has been found.
            // The sender gets the bounty
            require(msg.sender.send(bounty));
        }
    }

}