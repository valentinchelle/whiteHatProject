pragma solidity ^0.4.17;

contract CompanyContract{
    address public contractCreator;
    uint public bounty;
    bytes32 hashCode;

    function CompanyContract(string password) payable public {
        bounty = msg.value;
        contractCreator = msg.sender;
        hashCode = keccak256(password);

    }

    function passAttempt(string password) public {
        if(keccak256(password) == hashCode){
            // The pass has been found.
            // The sender gets the bounty
            require(msg.sender.send(bounty));
        }
    }

}